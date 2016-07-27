<?php namespace App\Http\Controllers\Auth;

use App\Core\Form\BaseForm;
use App\Http\Controllers\Controller;
use App\Services\Registration;
use App\Services\RegistrationAgencies;
use App\Services\RequestManager\RegisterOrganization;
use App\Services\RequestManager\RegisterUsers;
use App\Services\RequestManager\Register;
use App\Services\Verification;

/**
 * Class RegistrationController
 * @package App\Http\Controllers\Auth
 */
class RegistrationController extends Controller
{
    /**
     * @var BaseForm
     */
    protected $baseForm;
    /**
     * @var Registration
     */
    protected $registrationManager;
    /**
     * @var Verification
     */
    protected $verificationManager;
    /**
     * @var RegistrationAgencies
     */
    private $regAgencyManager;

    /**
     * @param BaseForm             $baseForm
     * @param Registration         $registrationManager
     * @param Verification         $verificationManager
     * @param RegistrationAgencies $regAgencyManager
     */
    public function __construct(BaseForm $baseForm, Registration $registrationManager, Verification $verificationManager, RegistrationAgencies $regAgencyManager)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->baseForm            = $baseForm;
        $this->registrationManager = $registrationManager;
        $this->verificationManager = $verificationManager;
        $this->regAgencyManager    = $regAgencyManager;
    }

    /**
     * returns registration view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $orgType      = $this->baseForm->getCodeList('OrganizationType', 'Organization', false);
        $countries    = $this->baseForm->getCodeList('Country', 'Organization', false);
        $orgRegAgency = $this->baseForm->getCodeList('OrganisationRegistrationAgency', 'Organization', false);
        $dbRegAgency  = $this->regAgencyManager->getRegAgenciesCode();
        $orgRegAgency = array_merge($orgRegAgency, $dbRegAgency);

        $dbRoles = \DB::table('role')->whereNotNull('permissions')->orderBy('role', 'desc')->get();
        $roles   = [];
        foreach ($dbRoles as $role) {
            $roles[$role->id] = $role->role;
        }

        return view('auth.register', compact('orgType', 'countries', 'orgRegAgency', 'roles'));
    }

    /**
     * save organization info and users
     * @param Register $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Register $request)
    {
        session()->forget('reg_info');
        $request = request();
        $users   = $request->get('users');
        $orgInfo = $request->get('organization');

        if ($organization = $this->registrationManager->register($orgInfo, $users)) {
            return $this->postRegistration($organization);
        } else {
            $response = ['type' => 'danger', 'code' => ['failed_registration']];

            return redirect()->back()->withInput()->withResponse($response);
        }
    }

    /**
     * sends emails to users after registration
     * @param $organization
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function postRegistration($organization)
    {
        $user = $organization->users->where('role_id', 1)->first();
        $this->verificationManager->sendVerificationEmail($user);

        return redirect()->to('/auth/login')->withMessage(
            sprintf(
                'A verification email has been sent to %s. Please check your email inbox and click on the link in the email to verify your email address.',
                $user->email
            )
        );
    }

    /**
     * show similar organizations
     * @param null $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSimilarOrganizations($type = null)
    {
        session()->put('reg_info', request()->except('_token'));
        $orgName = request('organization.organization_name');

        return view('auth.similarOrg', compact('orgName', 'type'));
    }

    /**
     * show same organization identifier verification
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSameOrgIdentifier()
    {
        $orgIdentifier = request('organization.organization_identifier');
        $orgInfo       = $this->registrationManager->checkOrgIdentifier($orgIdentifier);
        $orgName       = $orgInfo['org_name'];
        $adminName     = $orgInfo['admin_name'];

        return view('auth.sameOrgIdentifier', compact('orgName', 'adminName'));
    }

    /**
     * returns list of similar organizations
     * @param $orgName
     * @return array
     */
    public function listSimilarOrganizations($orgName)
    {
        $similarOrganizations = $this->registrationManager->getSimilarOrg($orgName);

        return $this->registrationManager->prepareSimilarOrg($similarOrganizations);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function submitSimilarOrganization()
    {
        if (($orgId = request('similar_organization')) && request('type') == 'admin') {
            if ($secondaryContact = $this->registrationManager->hasSecondaryContact($orgId)) {
                if ($this->registrationManager->sendRecoveryEmail($orgId, $secondaryContact['email'])) {
                    return redirect()->to('/')->withSecondaryContactName(trim($secondaryContact['first_name'] . ' ' . $secondaryContact['last_name']));
                } else {
                    return redirect()->back()->withErrors(['email' => 'Failed to send email.']);
                }
            } else {
                return redirect()->route('contact', ['no-secondary-contact-support']);
            }
        } elseif (!request('similar_organization') && (request('type') == 'admin' || request('type') == 'user')) {
            return redirect()->route('contact', ['not-my-organization']);
        }

        return redirect()->route('registration')->withTab('#tab-users');
    }

    /**
     * returns organization by identifier
     * @return array
     */
    public function checkOrgIdentifier()
    {
        if ($orgId = (request('org_id'))) {
            $orgInfo = $this->registrationManager->getOrganization($orgId);
        } else {
            $orgInfo = $this->registrationManager->checkOrgIdentifier(request('org_identifier'));
        }
        if ($orgInfo && ($adminEmail = getVal($orgInfo, ['admin_email']))) {
            session()->put('admin_email', $adminEmail);
        }

        return $orgInfo;
    }
}
