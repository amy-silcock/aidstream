<?php

namespace App\Http\Controllers\Complete;

use App;
use App\Core\V201\Forms\Organization\ReportingOrganizationInfoForm;
use App\Http\Controllers\Controller;
use App\Services\Organization\OrganizationManager;
use App\Services\RequestManager\Organization\SettingsRequestManager;
use App\Services\SettingsManager;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Kris\LaravelFormBuilder\FormBuilder;

class SettingsController extends Controller
{
    protected $organization;
    protected $orgReportingOrgInfoForm;
    protected $orgPublishingTypeForm;
    protected $orgRegistryInfoForm;
    protected $orgDefaultFieldValuesForm;
    protected $orgDefaultFieldGroupsForm;
    protected $org_id;

    /**
     * @param SettingsManager               $settingsManager
     * @param OrganizationManager           $organizationManager
     * @param ReportingOrganizationInfoForm $orgReportingOrgInfoForm
     */
    public function __construct(
        SettingsManager $settingsManager,
        OrganizationManager $organizationManager,
        ReportingOrganizationInfoForm $orgReportingOrgInfoForm
    ) {
        $this->middleware('auth');
        $this->settingsManager         = $settingsManager;
        $this->org_id                  = Session::get('org_id');
        $this->organizationManager     = $organizationManager;
        $this->settings                = $this->settingsManager->getSettings($this->org_id);
        $this->organization            = $this->organizationManager->getOrganization($this->org_id);
        $this->orgReportingOrgInfoForm = $orgReportingOrgInfoForm;
    }

    /**
     * Display a listing of the resource.
     *
     * @param FormBuilder $formBuilder
     *
     * @return Response
     */
    public function index(FormBuilder $formBuilder)
    {
        $model = [];
        if (isset($this->settings)) {
            $model['version_form']         = [['version' => $this->settings->version]];
            $model['publishing_type']      = [['publishing' => $this->settings->publishing_type]];
            $model['registry_info']        = $this->settings->registry_info;
            $model['default_field_values'] = $this->settings->default_field_values;
            $model['default_field_groups'] = $this->settings->default_field_groups;
        } else {
            $data  = '{"version_form":[{"version":"2.01"}],"reporting_organization_info":[{"reporting_organization_identifier":"","reporting_organization_type":"10","organization_name":"","reporting_organization_language":"es"}],"publishing_type":[{"publishing":"unsegmented"}],"registry_info":[{"publisher_id":"","api_id":"","publish_files: ":"no"}],"default_field_values":[{"default_currency":"AED","default_language":"es","default_hierarchy":"","default_collaboration_type":"1","default_flow_type":"10","default_finance_type":"310","default_aid_type":"A01","Default_tied_status":"3"}],"default_field_groups":[{"title":"Title","description":"Description","activity_status":"Activity Status","activity_date":"Activity Date","participating_org":"Participating Org","recipient_county":"Recipient Country","location":"Location","sector":"Sector","budget":"Budget","transaction":"Transaction","document_ink":"Document Link"}]}';
            $model = json_decode($data, true);
        }
        if (isset($this->organization)) {
            $model['reporting_organization_info'] = $this->organization->reporting_org;
        };
        $url         = 'settings.' . (isset($this->settings) ? 'update' : 'store');
        $method      = isset($this->settings) ? 'PUT' : 'POST';
        $formOptions = [
            'method' => $method,
            'url'    => route($url),
        ];
        if (!empty($model)) {
            $formOptions['model'] = $model;
        }
        $form = $formBuilder->create('App\Core\V201\Forms\SettingsForm', $formOptions);

        return view('settings', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SettingsRequestManager $request
     *
     * @return Response
     */
    public function store(SettingsRequestManager $request)
    {
        $input = Input::all();
        $this->settingsManager->storeSettings($input, $this->organization);
        Session::flash('message', 'Successfully Edit');

        return Redirect::to('/');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int                    $id
     * @param SettingsRequestManager $request
     *
     * @return Response
     */
    public function update($id, SettingsRequestManager $request)
    {
        $input = Input::all();
        $this->settingsManager->updateSettings($input, $this->organization, $this->settings);
        Session::flash('message', 'Successfully Edit');

        return Redirect::to('/');
    }
}
