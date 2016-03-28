<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\LegacyDataManager;
use App\Services\FormCreator\Activity\LegacyData as LegacyDataForm;
use App\Services\Activity\ActivityManager;
use App\Http\Requests\Request;
use App\Services\RequestManager\Activity\LegacyData as LegacyDataRequestManager;

/**
 * Class LegacyDataController
 * @package App\Http\Controllers\Complete\Activity
 */
class LegacyDataController extends Controller
{

    function __construct(LegacyDataManager $legacyDataManager, LegacyDataForm $legacyDataForm, ActivityManager $activityManager)
    {
        $this->middleware('auth');
        $this->legacyDataManager = $legacyDataManager;
        $this->legacyDataForm    = $legacyDataForm;
        $this->activityManager   = $activityManager;
    }

    public function index($id)
    {
        $legacyData   = $this->legacyDataManager->getLegacyData($id);
        $activityData = $this->activityManager->getActivityData($id);
        $form         = $this->legacyDataForm->editForm($legacyData, $id);

        return view('Activity.legacyData.edit', compact('form', 'activityData', 'id'));
    }

    public function update($id, Request $request, LegacyDataRequestManager $legacyDataRequestManager)
    {
        $activityData = $this->activityManager->getActivityData($id);
        $this->authorizeByRequestType($activityData, 'legacy_data');
        $legacyData   = $request->all();
        if ($this->legacyDataManager->update($legacyData, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => 'Legacy Data']]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => 'Legacy Data']]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
