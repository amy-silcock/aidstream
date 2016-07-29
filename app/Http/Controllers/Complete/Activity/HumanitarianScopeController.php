<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\HumanitarianScopeManager;
use App\Services\FormCreator\Activity\HumanitarianScope;
use App\Services\RequestManager\Activity\HumanitarianScope as HumanitarianScopeRequest;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Class HumanitarianScopeController
 * @package App\Http\Controllers\Complete\Activity
 */
class HumanitarianScopeController extends Controller
{
    /**
     * @var HumanitarianScope
     */
    protected $humanitarianScopeForm;
    /**
     * @var HumanitarianScopeManager
     */
    protected $humanitarianScopeManager;

    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * @param HumanitarianScopeManager $humanitarianScopeManager
     * @param HumanitarianScope        $humanitarianScopeForm
     * @param ActivityManager          $activityManager
     */
    function __construct(HumanitarianScopeManager $humanitarianScopeManager, HumanitarianScope $humanitarianScopeForm, ActivityManager $activityManager)
    {
        $this->middleware('auth');
        $this->middleware('role');
        $this->activityManager          = $activityManager;
        $this->humanitarianScopeForm    = $humanitarianScopeForm;
        $this->humanitarianScopeManager = $humanitarianScopeManager;
    }

    /**
     * view form
     * @param $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $activityData  = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $countryBudgetItem = $this->humanitarianScopeManager->getActivityHumanitarianScopeData($id);
        $form              = $this->humanitarianScopeForm->editForm($countryBudgetItem, $id);

        return view('Activity.humanitarianScope.edit', compact('form', 'activityData', 'id'));
    }

    /**
     * update humanitarian scope
     * @param                          $id
     * @param Request                  $request
     * @param HumanitarianScopeRequest $humanitarianScopeRequest
     * @return mixed
     */
    public function update($id, Request $request, HumanitarianScopeRequest $humanitarianScopeRequest)
    {
        $activityData  = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $this->authorizeByRequestType($activityData, 'humanitarian_scope');
        $humanitarianScope = $request->all();
        if ($this->humanitarianScopeManager->update($humanitarianScope, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => 'Humanitarian Scope']]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => 'Humanitarian Scope']]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
