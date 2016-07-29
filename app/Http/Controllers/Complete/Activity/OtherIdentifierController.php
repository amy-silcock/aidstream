<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\OtherIdentifierManager;
use App\Services\FormCreator\Activity\OtherIdentifierForm;
use App\Services\RequestManager\Activity\OtherIdentifierRequestManager;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Class OtherIdentifierController
 * @package app\Http\Controllers\Complete\Activity
 */
class OtherIdentifierController extends Controller
{
    /**
     * @var OtherIdentifierManager
     */
    protected $otherIdentifierManager;
    /**
     * @var OtherIdentifierForm
     */
    protected $otherIdentifierForm;

    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * @param OtherIdentifierManager $otherIdentifierManager
     * @param OtherIdentifierForm    $otherIdentifierForm
     * @param ActivityManager        $activityManager
     */
    public function __construct(OtherIdentifierManager $otherIdentifierManager, OtherIdentifierForm $otherIdentifierForm, ActivityManager $activityManager)
    {
        $this->middleware('auth');
        $this->middleware('role');
        $this->otherIdentifierManager = $otherIdentifierManager;
        $this->otherIdentifierForm    = $otherIdentifierForm;
        $this->activityManager        = $activityManager;
    }

    /**
     * view other identifier add or edit page
     * @param $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $activityData    = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $otherIdentifier = $this->otherIdentifierManager->getOtherIdentifierData($id);
        $form            = $this->otherIdentifierForm->editForm($otherIdentifier, $id);

        return view(
            'Activity.otherIdentifier.otherIdentifier',
            compact('form', 'otherIdentifier', 'id', 'activityData')
        );
    }

    /**
     * update activity Other Identifier
     * @param OtherIdentifierRequestManager $otherIdentifierRequestManager
     * @param Request                       $request
     * @param                               $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OtherIdentifierRequestManager $otherIdentifierRequestManager, Request $request, $id)
    {
        $activityData = $this->otherIdentifierManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $this->authorizeByRequestType($activityData, 'other_identifier');
        $input = $request->all();
        if ($this->otherIdentifierManager->update($input, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => 'Other Activity Identifier']]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => 'Other Activity Identifier']]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
