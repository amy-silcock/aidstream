<?php namespace App\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ContactInfoManager;
use App\Services\Activity\ActivityManager;
use App\Services\FormCreator\Activity\ContactInfo as ContactInfoForm;
use App\Services\RequestManager\Activity\ContactInfo as ContactInfoRequestManager;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Class ContactInfoController
 * @package app\Http\Controllers\Complete\Activity
 */
class ContactInfoController extends Controller
{
    /**
     * @var ActivityManager
     */
    protected $activityManager;
    /**
     * @var ContactInfoForm
     */
    protected $contactInfoForm;
    /**
     * @var ContactInfoManager
     */
    protected $contactInfoManager;

    /**
     * @param ContactInfoManager $contactInfoManager
     * @param ContactInfoForm    $contactInfoForm
     * @param ActivityManager    $activityManager
     */
    function __construct(
        ContactInfoManager $contactInfoManager,
        ContactInfoForm $contactInfoForm,
        ActivityManager $activityManager
    ) {
        $this->middleware('auth');
        $this->activityManager    = $activityManager;
        $this->contactInfoForm    = $contactInfoForm;
        $this->contactInfoManager = $contactInfoManager;
    }

    /**
     * returns the activity contact info edit form
     * @param $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $ContactInfo = $this->contactInfoManager->getContactInfoData($id);
        $form        = $this->contactInfoForm->editForm($ContactInfo, $id);

        return view(
            'Activity.contactInfo.edit',
            compact('form', 'activityData', 'id')
        );
    }

    /**
     * updates activity contact info
     * @param                           $id
     * @param Request                   $request
     * @param ContactInfoRequestManager $contactInfoRequestManager
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request, ContactInfoRequestManager $contactInfoRequestManager)
    {
        $activityData = $this->activityManager->getActivityData($id);

        if (Gate::denies('ownership', $activityData)) {
            return redirect()->back()->withResponse($this->getNoPrivilegesMessage());
        }

        $this->authorizeByRequestType($activityData, 'contact_info');
        $contactInfo = $request->all();
        if ($this->contactInfoManager->update($contactInfo, $activityData)) {
            $this->activityManager->resetActivityWorkflow($id);
            $response = ['type' => 'success', 'code' => ['updated', ['name' => trans('element.contact_info')]]];

            return redirect()->to(sprintf('/activity/%s', $id))->withResponse($response);
        }
        $response = ['type' => 'danger', 'code' => ['update_failed', ['name' => trans('element.contact_info')]]];

        return redirect()->back()->withInput()->withResponse($response);
    }
}
