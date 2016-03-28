<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityStatus as ActivityStatusManager;
use App\Services\Activity\ActivityManager;
use App\Services\FormCreator\Activity\ActivityStatus as ActivityStatusForm;
use App\Services\RequestManager\Activity\ActivityStatus as ActivityStatusRequestManager;
use App\Http\Requests\Request;

/**
 * Class ActivityStatusController
 * @package app\Http\Controllers\Complete\Activity
 */
class ActivityStatusController extends Controller
{
    /**
     * @var ActivityStatusForm
     */
    protected $activityStatusForm;
    /**
     * @var ActivityStatusManager
     */
    protected $activityStatusManager;
    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * @param ActivityStatusManager $activityStatusManager
     * @param ActivityStatusForm    $activityStatusForm
     * @param ActivityManager       $activityManager
     */
    function __construct(
        ActivityStatusManager $activityStatusManager,
        ActivityStatusForm $activityStatusForm,
        ActivityManager $activityManager
    ) {
        $this->middleware('auth');
        $this->activityStatusForm    = $activityStatusForm;
        $this->activityStatusManager = $activityStatusManager;
        $this->activityManager       = $activityManager;
    }

    /**
     * returns the activity status edit form
     * @param $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $activityStatus = $this->activityStatusManager->getActivityStatusData($id);
        $activityData   = $this->activityManager->getActivityData($id);
        $form           = $this->activityStatusForm->editForm($activityStatus, $id);

        return view(
            'Activity.activityStatus.edit',
            compact('form', 'activityData', 'id')
        );
    }

    /**
     * updates activity status
     * @param                              $id
     * @param Request                      $request
     * @param ActivityStatusRequestManager $activityStatusRequestManager
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request, ActivityStatusRequestManager $activityStatusRequestManager)
    {
        $activityData   = $this->activityManager->getActivityData($id);
        $this->authorizeByRequestType($activityData, 'activity_status');
        $activityStatus = $request->all();
        if ($this->activityStatusManager->update($activityStatus, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => 'Activity Status']]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => 'Activity Status']]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
