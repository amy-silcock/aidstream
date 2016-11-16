<?php

namespace App\Http\Controllers;

use App\Models\Organization\Organization;
use App\Services\UserOnBoarding\UserOnBoardingService;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/**
 * Class UserOnBoardingController
 * @package App\Http\Controllers
 */
class UserOnBoardingController extends Controller
{
    /**
     * @var UserOnBoardingService
     */
    protected $userOnBoardingService;

    /**
     * UserOnBoardingController constructor.
     * @param UserOnBoardingService $userOnBoardingService
     */
    public function __construct(UserOnBoardingService $userOnBoardingService)
    {
        $this->userOnBoardingService = $userOnBoardingService;
    }

    /**
     * Start welcome page of user onboarding.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function welcome()
    {
        $firstname = Auth::user()->first_name;
        $lastname  = Auth::user()->last_name;

        if ((!Auth::user()->userOnBoarding) || (!session('first_login'))) {
            return redirect()->to('/activity');
        }

        return view('onBoarding.welcome', compact('firstname', 'lastname'));
    }

    /**
     * Store publisher and api id of settings.
     */
    public function storePublisherAndApiId()
    {
        $organization      = $this->getOrganization();
        $publisherId       = Input::get('publisherId');
        $apiId             = Input::get('apiId');
        $publisherIdStatus = Input::get('publisherIdStatus');
        $apiIdStatus       = Input::get('apiIdStatus');

        $this->userOnBoardingService->storePublisherAndApiKey($organization, $publisherId, $apiId, $publisherIdStatus, $apiIdStatus);
    }

    /**
     * Store publishing type settings.
     */
    public function storePublishingType()
    {
        $organization   = $this->getOrganization();
        $publishingType = Input::get('publishing');

        $this->userOnBoardingService->storePublishingType($organization, $publishingType);
    }

    /**
     * Store automatic publish to registry to settings.
     */
    public function storePublishFiles()
    {
        $organization = $this->getOrganization();
        $publishFiles = Input::get('publish_files');

        $this->userOnBoardingService->storePublishFiles($organization, $publishFiles);
    }

    /**
     * Store activity elements checklist of settings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeActivityElementsChecklist(Request $request)
    {
        $organization         = $this->getOrganization();
        $default_field_groups = $request->get('default_field_groups');

        $this->userOnBoardingService->storeActivityElementsChecklist($default_field_groups, $organization);
        $completedSteps = (array) auth()->user()->userOnBoarding->settings_completed_steps;

        $redirectPath = (in_array(5, $completedSteps)) ? '/activity' : '/default-values#5';

        return redirect()->to($redirectPath);
    }

    /**
     * Store default values of settings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDefaultValues(Request $request)
    {
        $organization = $this->getOrganization();
        $this->userOnBoardingService->storeDefaultValues($request, $organization);
        $completedSteps = $this->userOnBoardingService->getCompletedSettingsSteps();
        $status         = $this->userOnBoardingService->isAllStepsCompleted();

        return redirect()->to('/activity')->with('completedSteps', $completedSteps);
    }

    /**
     * Return explore later view.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
//    public function exploreLater()
//    {
//        if (!Auth::user()->userOnBoarding) {
//            return redirect()->to('/activity');
//        }
//
//        if (Auth::user()->userOnBoarding->completed_tour) {
//            return redirect()->back();
//        }
//        $completedSteps = $this->userOnBoardingService->getCompletedSettingsSteps();
//        $status         = $this->userOnBoardingService->isAllStepsCompleted();
//        Session::put('first_login', true);
//
//        return view('onBoarding.exploreLater', compact('completedSteps', 'status'));
//    }

//    /**
//     * returns continue exploring view.
//     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
//     */
//    public function continueExploring()
//    {
//        if (!Auth::user()->userOnBoarding) {
//            return redirect()->to('/activity');
//        }

//        $firstname      = Auth::user()->first_name;
//        $completedSteps = $this->userOnBoardingService->getCompletedSettingsSteps();
//        $status         = $this->userOnBoardingService->isAllStepsCompleted();
//        return
//        return view(sprintf('onBoarding.continueExploring'), compact('firstname', 'completedSteps', 'status'));
//    }

    /**
     *  Complete user on boarding process
     */
//    public function completeOnBoarding()
//    {
//        $this->userOnBoardingService->completeTour();
//    }

    /**
     * Returns organization currently in session.
     * @return mixed
     */
    public function getOrganization()
    {
        $org_id       = session('org_id');
        $organization = Organization::findorfail($org_id);

        return $organization;
    }

    /**
     * Create a new On boarding for newly created user.
     * @param $userId
     */
    public function create($userId)
    {
        $this->userOnBoardingService->create($userId);
    }

    /**
     * Store dashboard hints status
     * @return int
     */
    public function storeHintStatus()
    {
        $step        = (int) Input::get('status');
        $hintsStatus = ($step === 1) ? true : false;
        $status      = $this->userOnBoardingService->storeHintStatus($hintsStatus);

        return ($status) ? 'success' : 'failed';
    }

    public function firstIncompleteStep()
    {
        $completedSteps = (array) auth()->user()->userOnBoarding->settings_completed_steps;
        $redirectPath   = '/publishing-settings#1';

        $paths = [
            1 => '/publishing-settings#1',
            2 => '/publishing-settings#2',
            3 => '/publishing-settings#3',
            4 => '/activity-elements-checklist#4',
            5 => '/default-values#5'
        ];

        foreach ($completedSteps as $step) {
            if (array_key_exists($step, $paths)) {
                unset($paths[$step]);
            }
        }
        $redirectPath = (count($paths) < 0) ? $redirectPath : array_shift($paths);

        return redirect()->to($redirectPath);
    }
}
