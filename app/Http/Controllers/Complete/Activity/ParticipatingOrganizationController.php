<?php

namespace app\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ParticipatingOrganizationManager;
use App\Services\ActivityLog\ActivityManager;
use App\Services\FormCreator\Activity\ParticipatingOrganization as ParticipatingOrganizationForm;
use App\Services\RequestManager\Activity\ParticipatingOrganization as ParticipatingOrganizationRequestManager;
use Illuminate\Http\Request;

/**
 * Class ParticipatingOrganizationController.
 */
class ParticipatingOrganizationController extends Controller
{
    /**
     * @var ActivityManager
     */
    protected $activityManager;
    /**
     * @var ParticipatingOrganizationForm
     */
    protected $participatingOrganizationForm;
    /**
     * @var ParticipatingOrganizationManager
     */
    protected $participatingOrganizationManager;

    /**
     * @param ParticipatingOrganizationManager $participatingOrganizationManager
     * @param ParticipatingOrganizationForm    $participatingOrganizationForm
     * @param ActivityManager                  $activityManager
     */
    public function __construct(
        ParticipatingOrganizationManager $participatingOrganizationManager,
        ParticipatingOrganizationForm $participatingOrganizationForm,
        ActivityManager $activityManager
    ) {
        $this->middleware('auth');
        $this->activityManager                  = $activityManager;
        $this->participatingOrganizationForm    = $participatingOrganizationForm;
        $this->participatingOrganizationManager = $participatingOrganizationManager;
    }

    /**
     * returns the activity contact info edit form.
     *
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $participatingOrganization = $this->participatingOrganizationManager->getParticipatingOrganizationData($id);
        $activityData              = $this->activityManager->getActivityData($id);
        $form                      = $this->participatingOrganizationForm->editForm($participatingOrganization, $id);

        return view(
            'Activity.participatingOrganization.edit',
            compact('form', 'activityData', 'id')
        );
    }

    /**
     * updates activity participating organization.
     *
     * @param                                         $id
     * @param Request                                 $request
     * @param ParticipatingOrganizationRequestManager $participatingOrganizationRequestManager
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        $id,
        Request $request,
        ParticipatingOrganizationRequestManager $participatingOrganizationRequestManager
    ) {
        $participatingOrganization = $request->all();
        $activityData              = $this->activityManager->getActivityData($id);
        if ($this->participatingOrganizationManager->update($participatingOrganization, $activityData)) {
            return redirect()->to(sprintf('/activity/%s', $id))->withMessage(
                'Activity Participating Organization Updated !'
            );
        }

        return redirect()->back();
    }
}
