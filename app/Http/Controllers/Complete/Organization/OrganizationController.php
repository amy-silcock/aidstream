<?php namespace App\Http\Controllers\Complete\Organization;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\SettingsManager;
use App\Services\Organization\OrganizationManager;
use App\Services\FormCreator\Organization\OrgReportingOrgForm;
use App\Http\Requests\Request;
use App\Services\Organization\OrgNameManager;

/**
 * Class OrganizationController
 * @package App\Http\Controllers\Complete\Organization
 */
class OrganizationController extends Controller
{
    /**
     * @var OrganizationManager
     */
    protected $organizationManager;
    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @var OrgReportingOrgForm
     */
    protected $orgReportingOrgFormCreator;

    /**
     * @var OrgNameManager
     */
    protected $nameManager;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * Create a new controller instance.
     *
     * @param SettingsManager     $settingsManager
     * @param OrganizationManager $organizationManager
     * @param OrgReportingOrgForm $orgReportingOrgFormCreator
     * @param OrgNameManager      $nameManager
//     * @param Request             $request
     * @param ActivityManager     $activityManager
     */
    public function __construct(
        SettingsManager $settingsManager,
        OrganizationManager $organizationManager,
        OrgReportingOrgForm $orgReportingOrgFormCreator,
        OrgNameManager $nameManager,
        ActivityManager $activityManager
    ) {
        $this->settingsManager            = $settingsManager;
        $this->organizationManager        = $organizationManager;
        $this->orgReportingOrgFormCreator = $orgReportingOrgFormCreator;
        $this->nameManager                = $nameManager;
//        $this->request                    = $request;
        $this->activityManager            = $activityManager;
        $this->middleware('auth');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $organization = $this->organizationManager->getOrganization($id);

        if (!isset($organization->reporting_org[0])) {
            $response = ['type' => 'warning', 'code' => ['settings', ['name' => 'organization']]];

            return redirect('/settings')->withResponse($response);
        }

        $organizationData              = $this->nameManager->getOrganizationData($id);
        $reporting_org                 = (array) $organization->reporting_org[0];
        $org_name                      = (array) $organizationData->name;
        $total_budget                  = (array) $organizationData->total_budget;
        $recipient_organization_budget = (array) $organizationData->recipient_organization_budget;
        $recipient_country_budget      = (array) $organizationData->recipient_country_budget;
        $document_link                 = (array) $organizationData->document_link;
        $status                        = $organizationData->status;

        $status_label     = ['draft', 'completed', 'verified', 'published'];
        $btn_status_label = ['Complete', 'Verify', 'Publish'];
        $btn_text         = $status > 2 ? "" : $btn_status_label[$status];
        $extra            = ['status_label' => $status_label, 'btn_text' => $btn_text];

        return view(
            'Organization/show',
            compact(
                'organization',
                'reporting_org',
                'org_name',
                'total_budget',
                'recipient_organization_budget',
                'recipient_country_budget',
                'document_link',
                'status',
                'extra'
            )
        );
    }

    /**
     * @param         $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id, Request $request)
    {
        $input            = $request->all();
        $organization     = $this->organizationManager->getOrganization($id);
        $organizationData = $this->organizationManager->getOrganizationData($id);
        $settings         = $this->settingsManager->getSettings($id);
        $status           = $input['status'];

        $orgElem    = $this->organizationManager->getOrganizationElement();
        $xmlService = $orgElem->getOrgXmlService();

        if ($status === "1") {
            $messages = $xmlService->validateOrgSchema($organization, $organizationData, $settings, $orgElem);
            if ($messages !== '') {
                $response = ['type' => 'danger', 'messages' => $messages];

                return redirect()->back()->withResponse($response);
            }
        } else {
            if ($status === "3") {
                $xmlService->generateOrgXml($organization, $organizationData, $settings, $orgElem);
            }
        }

        $statusLabel = ['Completed', 'Verified', 'Published'];
        $response    = ($this->organizationManager->updateStatus($input, $organizationData)) ?
            ['type' => 'success', 'code' => ['org_statuses', ['name' => $statusLabel[$status - 1]]]] :
            ['type' => 'danger', 'code' => ['org_statuses_failed', ['name' => $statusLabel[$status - 1]]]];

        return redirect()->back()->withResponse($response);
    }

    /**
     * write brief description
     * @param $id
     * @return \Illuminate\View\View
     */
    public function showIdentifier($id)
    {
        $organization = $this->organizationManager->getOrganization($id);
        $data         = $organization->reporting_org;
        $form         = $this->orgReportingOrgFormCreator->editForm($data, $organization);

        return view('Organization.identifier.edit', compact('form', 'organization'));
    }

    /**
     * List published files for an
     * @param Request $request
     * @param string  $action
     * @param string  $id
     * @return \Illuminate\View\View
     */
    public function listPublishedFiles(Request $request, $action = '', $id = '')
    {
        if ($action == 'delete') {
            $result   = $this->organizationManager->deletePublishedFile($id);
            $message  = $result ? 'File deleted successfully' : 'File couldn\'t be deleted.';
            $type     = $result ? 'success' : 'danger';
            $response = ['type' => $type, 'code' => ['transfer_message', ['name' => $message]]];

            return redirect()->back()->withResponse($response);
        }

        $org_id        = $request->session()->get('org_id');
        $list          = $this->organizationManager->getPublishedFiles($org_id);
        $activity_list = $this->activityManager->getActivityPublishedFiles($org_id);

        return view('published-files', compact('list', 'activity_list'));
    }
}
