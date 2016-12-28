<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity\Activity;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\IatiIdentifierManager;
use App\Services\FormCreator\Activity\Identifier;
use App\Services\Organization\OrganizationManager;
use App\Services\RequestManager\Activity\IatiIdentifierRequestManager;
use App\Http\Requests\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Gate;

class IatiIdentifierController extends Controller
{
    /**
     * @var Activity
     */
    protected $activity;
    /**
     * @var IatiIdentifierManager
     */
    protected $iatiIdentifierManager;
    /**
     * @var Identifier
     */
    protected $identifier;
    /**
     * @var OrganizationManager
     */
    protected $organizationManager;
    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * @param Activity              $activity
     * @param IatiIdentifierManager $iatiIdentifierManager
     * @param Identifier            $identifier
     * @param OrganizationManager   $organizationManager
     * @param SessionManager        $sessionManager
     * @param ActivityManager       $activityManager
     */
    function __construct(
        Activity $activity,
        IatiIdentifierManager $iatiIdentifierManager,
        Identifier $identifier,
        OrganizationManager $organizationManager,
        SessionManager $sessionManager,
        ActivityManager $activityManager
    ) {
        $this->middleware('auth');

        $this->activity              = $activity;
        $this->iatiIdentifierManager = $iatiIdentifierManager;
        $this->identifier            = $identifier;
        $this->organizationManager   = $organizationManager;
        $this->sessionManager        = $sessionManager;
        $this->organization_id       = $this->sessionManager->get('org_id');
        $this->activityManager       = $activityManager;
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    function index($id)
    {
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $iatiIdentifier        = $this->iatiIdentifierManager->getIatiIdentifierData($id);
        $organization          = $this->organizationManager->getOrganization($this->organization_id);
        $reportingOrganization = $organization->reporting_org;
        $form                  = $this->identifier->editForm($iatiIdentifier, $id);

        return view('Activity.iatiIdentifier.iatiIdentifier', compact('form', 'reportingOrganization', 'id'));
    }

    /**
     * @param                              $activityId
     * @param IatiIdentifierRequestManager $iatiIdentifierRequestManager
     * @param Request                      $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($activityId, IatiIdentifierRequestManager $iatiIdentifierRequestManager, Request $request)
    {
        $activityData = $this->activityManager->getActivityData($activityId);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $iatiIdentifierData = $this->iatiIdentifierManager->getActivityData($activityId);
        $this->authorizeByRequestType($iatiIdentifierData, 'identifier');
        $input = $request->all();

        if ($this->iatiIdentifierManager->update($input, $iatiIdentifierData)) {
            $this->activityManager->resetActivityWorkflow($activityId);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => trans('title.activity_iati_identifier')]]];

            return redirect()->to(sprintf('/activity/%s', $activityId))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => trans('title.activity_iati_identifier')]]];

        return redirect()->route('activity.iati-identifier.index', $activityId)->withInput()->withResponse($response);
    }
}
