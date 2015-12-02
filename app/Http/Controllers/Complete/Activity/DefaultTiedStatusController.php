<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\DefaultTiedStatusManager;
use App\Services\FormCreator\Activity\DefaultTiedStatus as DefaultTiedStatusForm;
use App\Services\RequestManager\Activity\DefaultTiedStatus as DefaultTiedStatusRequestManager;
use Illuminate\Http\Request;

/**
 * Class DefaultTiedStatusController
 * @package App\Http\Controllers\Complete\Activity
 */
class DefaultTiedStatusController extends Controller
{
    /**
     * @var ActivityManager
     */
    protected $activityManager;
    /**
     * @var DefaultTiedStatusManager
     */
    protected $defaultTiedStatusManager;
    /**
     * @var DefaultTiedStatusForm
     */
    protected $defaultTiedStatusForm;

    /**
     * @param DefaultTiedStatusManager $defaultTiedStatusManager
     * @param DefaultTiedStatusForm    $defaultTiedStatusForm
     * @param ActivityManager          $activityManager
     */
    function __construct(DefaultTiedStatusManager $defaultTiedStatusManager, DefaultTiedStatusForm $defaultTiedStatusForm, ActivityManager $activityManager)
    {
        $this->middleware('auth');
        $this->activityManager          = $activityManager;
        $this->defaultTiedStatusManager = $defaultTiedStatusManager;
        $this->defaultTiedStatusForm    = $defaultTiedStatusForm;
    }

    /**
     * returns the activity default tied status edit form
     * @param $id
     * @return \Illuminate\View\View
     */
    public function  index($id)
    {
        $defaultTiedStatus = $this->defaultTiedStatusManager->getDefaultTiedStatusData($id);
        $activityData      = $this->activityManager->getActivityData($id);
        $form              = $this->defaultTiedStatusForm->editForm($defaultTiedStatus, $id);

        return view('Activity.defaultTiedStatus.edit', compact('form', 'activityData', 'id'));
    }

    /**
     * updates activity default tied status
     * @param                                 $id
     * @param Request                         $request
     * @param DefaultTiedStatusRequestManager $defaultTiedStatusRequestManager
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request, DefaultTiedStatusRequestManager $defaultTiedStatusRequestManager)
    {
        $this->authorize(['edit_activity', 'add_activity']);
        $defaultTiedStatus = $request->all();
        $activityData      = $this->activityManager->getActivityData($id);
        if ($this->defaultTiedStatusManager->update($defaultTiedStatus, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);

            return redirect()->to(sprintf('/activity/%s', $id))->withMessage('Activity Default Tied Status updated!');
        }

        return redirect()->back();
    }
}
