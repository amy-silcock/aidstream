<?php

namespace app\Http\Controllers\Complete\Activity;

use App\Http\Controllers\Controller;
use App\Services\Activity\ActivityManager;
use App\Services\Activity\RecipientRegionManager;
use App\Services\FormCreator\Activity\RecipientRegion as RecipientRegionForm;
use App\Services\RequestManager\Activity\RecipientRegion as RecipientRegionRequestManager;
use Illuminate\Http\Request;

class RecipientRegionController extends Controller
{
    /**
     * @var ActivityManager
     */
    protected $activityManager;
    /**
     * @var RecipientRegionForm
     */
    protected $recipientRegionForm;
    /**
     * @var RecipientRegionManager
     */
    protected $recipientRegionManager;

    /**
     * @param RecipientRegionManager $recipientRegionManager
     * @param RecipientRegionForm    $recipientRegionForm
     * @param ActivityManager        $activityManager
     */
    public function __construct(
        RecipientRegionManager $recipientRegionManager,
        RecipientRegionForm $recipientRegionForm,
        ActivityManager $activityManager
    ) {
        $this->middleware('auth');
        $this->activityManager        = $activityManager;
        $this->recipientRegionForm    = $recipientRegionForm;
        $this->recipientRegionManager = $recipientRegionManager;
    }

    /**
     * returns the activity recipient region edit form.
     *
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $recipientRegion = $this->recipientRegionManager->getRecipientRegionData($id);
        $activityData    = $this->activityManager->getActivityData($id);
        $form            = $this->recipientRegionForm->editForm($recipientRegion, $id);

        return view(
            'Activity.recipientRegion.edit',
            compact('form', 'activityData', 'id')
        );
    }

    /**
     * updates activity recipient region.
     *
     * @param                               $id
     * @param Request                       $request
     * @param RecipientRegionRequestManager $recipientRegionRequestManager
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request, RecipientRegionRequestManager $recipientRegionRequestManager)
    {
        $recipientRegion = $request->all();
        $activityData    = $this->activityManager->getActivityData($id);
        if ($this->recipientRegionManager->update($recipientRegion, $activityData)) {
            return redirect()->to(sprintf('/activity/%s', $id))->withMessage(
                'Activity Recipient Region Updated !'
            );
        }

        return redirect()->back();
    }
}
