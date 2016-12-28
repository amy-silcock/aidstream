<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\DefaultTiedStatusManager;
use App\Services\FormCreator\Activity\DefaultTiedStatus as DefaultTiedStatusForm;
use App\Services\RequestManager\Activity\DefaultTiedStatus as DefaultTiedStatusRequestManager;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Gate;

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
    public function index($id)
    {
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $defaultTiedStatus = $this->defaultTiedStatusManager->getDefaultTiedStatusData($id);
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
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $this->authorizeByRequestType($activityData, 'default_tied_status');
        $defaultTiedStatus = $request->all();
        if ($this->defaultTiedStatusManager->update($defaultTiedStatus, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => trans('element.default_tied_status')]]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => trans('element.default_tied_status')]]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
