<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ConditionManager;
use App\Services\FormCreator\Activity\Condition as ConditionForm;
use App\Services\Activity\ActivityManager;
use Illuminate\Http\Request;
use App\Services\RequestManager\Activity\Condition as ConditionRequestManager;

/**
 * Class ConditionController
 * @package App\Http\Controllers\Complete\Activity
 */
class ConditionController extends Controller
{
    function __construct(ConditionManager $conditionManager, ConditionForm $conditionForm, ActivityManager $activityManager)
    {
        $this->middleware('auth');
        $this->conditionManager = $conditionManager;
        $this->conditionForm    = $conditionForm;
        $this->activityManager  = $activityManager;
    }

    public function index($id)
    {
        $condition    = $this->conditionManager->getConditionData($id);
        $activityData = $this->activityManager->getActivityData($id);
        $form         = $this->conditionForm->editForm($condition, $id);

        return view('Activity.condition.edit', compact('form', 'activityData', 'id'));
    }

    public function update($id, Request $request, ConditionRequestManager $conditionRequestManager)
    {
        $condition    = $request->except(['_token', '_method']);
        $activityData = $this->activityManager->getActivityData($id);
        if ($this->conditionManager->update($condition, $activityData)) {
            return redirect()->to(sprintf('/activity/%s', $id))->withMessage('Condition Updated !');
        }

        return redirect()->back();
    }
}
