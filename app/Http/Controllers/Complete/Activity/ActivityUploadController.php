<?php namespace App\Http\Controllers\Complete\Activity;

use App\Core\V201\Repositories\Activity\IatiIdentifierRepository;
use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\UploadActivityManager;
use App\Services\FormCreator\Activity\UploadActivity;
use App\Services\Organization\OrganizationManager;
use App\Services\RequestManager\Activity\CsvImportValidator;
use App\Services\RequestManager\Activity\UploadActivity as UploadActivityRequest;
use App\Http\Requests\Request;
use App\Services\SettingsManager;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ActivityUploadController
 * @package App\Http\Controllers\Complete\Activity
 */
class ActivityUploadController extends Controller
{
    /**
     * @var UploadActivity
     */
    protected $uploadActivity;

    /**
     * @var UploadActivityManager
     */
    protected $uploadActivityManager;

    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * id of active organization
     */
    protected $organizationId;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var OrganizationManager
     */
    protected $organizationManager;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @param OrganizationManager   $organizationManager
     * @param SessionManager        $sessionManager
     * @param ActivityManager       $activityManager
     * @param UploadActivityManager $uploadActivityManager
     * @param UploadActivity        $uploadActivity
     * @param SettingsManager       $settingsManager
     */
    function __construct(
        OrganizationManager $organizationManager,
        SessionManager $sessionManager,
        ActivityManager $activityManager,
        UploadActivityManager $uploadActivityManager,
        UploadActivity $uploadActivity,
        SettingsManager $settingsManager
    ) {
        $this->middleware('auth');
        $this->activityManager       = $activityManager;
        $this->uploadActivity        = $uploadActivity;
        $this->uploadActivityManager = $uploadActivityManager;
        $this->sessionManager        = $sessionManager;
        $this->organizationId        = $this->sessionManager->get('org_id');
        $this->organizationManager   = $organizationManager;
        $this->settingsManager       = $settingsManager;
    }

    /**
     * show the upload form
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $organization = $this->organizationManager->getOrganization($this->organizationId);
        if (!isset($organization->reporting_org[0])) {
            $response = ['type' => 'warning', 'code' => ['settings', ['name' => trans('global.activity')]]];

            return redirect('/settings')->withResponse($response);
        }
        $form = $this->uploadActivity->createForm();

        return view('Activity.upload', compact('form'));
    }

    /**
     * Stores the activity by uploading csv
     * @param Request                  $request
     * @param UploadActivityRequest    $uploadActivityRequest
     * @param CsvImportValidator       $csvImportValidator
     * @param IatiIdentifierRepository $iatiIdentifierRepository
     * @return $this
     */
    public function store(Request $request, UploadActivityRequest $uploadActivityRequest, CsvImportValidator $csvImportValidator, IatiIdentifierRepository $iatiIdentifierRepository)
    {
        $organization = $this->organizationManager->getOrganization($this->organizationId);

        $this->authorize('add_activity', $organization);
        $settings           = $this->settingsManager->getSettings($this->organizationId);
        $defaultFieldValues = $settings->default_field_values;
        $defaultFieldGroups = $settings->default_field_groups;

        if (!isset($organization->reporting_org[0])) {
            $response = ['type' => 'warning', 'code' => ['settings', ['name' => trans('global.activity')]]];

            return redirect('/settings')->withResponse($response);
        }

        $identifiers         = [];
        $activityIdentifiers = $iatiIdentifierRepository->getIdentifiersForOrganization();

        foreach ($activityIdentifiers as $identifier) {
            $identifiers[] = $identifier->identifier['activity_identifier'];
        }

        $file = $request->file('activity');

        $uploadedActivityArray = $this->uploadActivityManager->getVersion()->getExcel()->load($file)->toArray();
        $geopolitical          = $defaultFieldGroups[0]['Geopolitical Information'];

        foreach ($uploadedActivityArray as $activity) {
            $recipientRegionExist = array_key_exists('recipient_region', $geopolitical);
            if (!$recipientRegionExist && !is_null($activity['recipient_region'])) {

                return redirect()->back()
                                 ->withResponse(
                                     [
                                         'type' => 'danger',
                                         'code' => ['recipient_region_unselected_in_settings', ['name' => trans('global.activity')]]
                                     ]
                                 );
            }
        }

        if ($this->uploadActivityManager->isEmptyCsv($file)) {
            return redirect()->back()
                             ->withResponse(
                                 [
                                     'type' => 'danger',
                                     'code' => ['empty_template', ['name' => trans('global.activity')]]
                                 ]
                             );
        }

        $validator = $csvImportValidator->validator->isValidActivityCsv($file, $identifiers);
        if (!$validator) {
            $response = ['type' => 'danger', 'code' => ['csv_template_error', ['name' => '']]];

            return redirect()->back()->withResponse($response);
        } elseif ($validator->fails()) {
            $failedRows           = $validator->failures();
            $uploadedActivities   = $this->uploadActivityManager->getVersion()->getExcel()->load($file)->toArray();
            $validActivities      = array_keys(array_diff_key($uploadedActivities, $failedRows));
            $validActivityIndices = [];
            foreach ($validActivities as $validActivity) {
                $validActivityIndices[] = $validActivity + 1;
            }
            $filename = 'temporary-' . $this->organizationId . 'activity';

            $this->temporarilyStoreCsvFor($validActivities, $filename);

            $validCsvFilePath = storage_path() . '/exports/' . $filename . '.csv';

            if (!$this->saveValidatedActivities($validCsvFilePath, $defaultFieldValues)) {
                return redirect()->back()->withResponse(['type' => 'warning', 'code' => ['save_failed', ['name' => trans('global.activity')]]]);
            }

            return $this->invalidActivities($validator, $validActivityIndices, $failedRows);
        }
        $check = $this->uploadActivityManager->save($file, $organization, $defaultFieldValues);

        if (is_a($check, 'Illuminate\View\View')) {
            return $check;
        }

        $response = ($check) ? ['type' => 'success', 'code' => ['updated', ['name' => trans('global.activities')]]] : [
            'type' => 'danger',
            'code' => ['update_failed', ['name' => trans('global.activities')]]
        ];

        return redirect()->to(sprintf('/activity'))->withResponse($response);
    }

    /**
     * update activities by uploading csv
     * @param         $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        if (!$this->currentUserIsAuthorizedForActivity($id)) {
            return redirect()->route('activity.index')->withResponse($this->getNoPrivilegesMessage());
        }

        $activityDetails = $request->get('checkbox');
        $this->uploadActivityManager->update($activityDetails);
        $response = ['type' => 'success', 'code' => ['updated', ['name' => trans('global.activities')]]];

        return redirect()->to(sprintf('/activity'))->withResponse($response);
    }

    /**
     * Save only those Activities passing Validation.
     * @param       $validCsvFilePath
     * @param array $defaultFieldValues
     * @return bool|null
     */
    protected function saveValidatedActivities($validCsvFilePath, array $defaultFieldValues)
    {
        if ($this->uploadActivityManager->save($validCsvFilePath, $this->organizationManager->getOrganization($this->organizationId), $defaultFieldValues)) {
            File::delete($validCsvFilePath);

            return true;
        }

        return null;
    }

    /**
     * Return response with uploaded Activities (if any) with the correct validation error messages for those not added due to validation errors.
     * @param       $validator
     * @param array $validActivities
     * @param array $failedRows
     * @return mixed
     */
    protected function invalidActivities($validator, array $validActivities, array $failedRows)
    {
        $uploadedActivities = [];

        foreach ($validActivities as $index => $activity) {
            $uploadedActivities[] = $activity;
        }

        $difference = array_diff_key(array_values($uploadedActivities), array_values($failedRows));
        $messages   = [];
        $messages[] = sprintf(
            'Some invalid activities (at row(s): %s) did not get saved while the valid ones (at row(s): %s) have been saved.',
            implode(',', $failedRows),
            !empty($difference) ? implode(',', $uploadedActivities) : ''
        );
        $messages   = array_merge($messages, $validator->errors()->all());
        $response   = ['type' => 'warning', 'messages' => $messages];

        return redirect()->back()->withInput()->withResponse($response);
    }

    /**
     * Save a new csv for the valid Activities.
     * @param $validActivities
     * @param $filename
     */
    protected function temporarilyStoreCsvFor($validActivities, $filename)
    {
        Excel::create(
            $filename,
            function ($excel) use ($validActivities) {
                $excel->sheet(
                    'test',
                    function ($sheet) use ($validActivities) {
                        $sheet->with($validActivities);
                    }
                );
            }
        )->store('csv');
    }
}
