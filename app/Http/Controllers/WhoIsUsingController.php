<?php namespace App\Http\Controllers;

use App\Helpers\GetCodeName;
use App\Models\PerfectViewer\ActivitySnapshot;
use App\Services\Activity\ActivityManager;
use App\Services\Organization\OrganizationManager;
use App\Services\PerfectViewer\PerfectViewerManager;
use App\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * Class WhoIsUsingController
 * @package App\Http\Controllers
 */
class WhoIsUsingController extends Controller
{

    /**
     * @var ActivitySnapshot
     */
    protected $perfectActivityViewerManager;

    /**
     * WhoIsUsingController constructor.
     * @param ActivityManager      $activityManager
     * @param OrganizationManager  $organizationManager
     * @param User                 $user
     * @param PerfectViewerManager $perfectActivityViewerManager
     */
    function __construct(ActivityManager $activityManager, OrganizationManager $organizationManager, User $user, PerfectViewerManager $perfectActivityViewerManager)
    {
        $this->activityManager              = $activityManager;
        $this->orgManager                   = $organizationManager;
        $this->user                         = $user;
        $this->perfectActivityViewerManager = $perfectActivityViewerManager;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $organizationCount = $this->organizationQueryBuilder()->get()->count();

        return view('who-is-using', compact('organizationCount'));
    }

    /** Returns query of organizations published on Aidstream.
     * @return mixed
     */
    public function organizationQueryBuilder()
    {
        return $this->perfectActivityViewerManager->organizationQueryBuilder();
    }

    /**
     * return organization list
     * @param int $page
     * @param int $count
     * @return mixed
     */
    public function listOrganization($page = 0, $count = 20)
    {
        $skip                  = $page * $count;
        $data['next_page']     = $this->organizationQueryBuilder()->get()->count() > ($skip + $count);
        $data['organizations'] = $this->organizationQueryBuilder()->skip($skip)->take($count)->get();
        return $data;
    }


    /**
     * @param $organizationId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDataForOrganization($organizationId)
    {
        $organizationIdExists = $this->organizationQueryBuilder()->where('org_id', $organizationId)->get();

        if (count($organizationIdExists) == 0) {
            throw new NotFoundHttpException();
        }

        $snapshotData = $this->perfectActivityViewerManager->getSnapshotWithOrgId($organizationId);

        dd($organizationIdExists, $snapshotData);

        $data               = $this->activityManager->getDataForOrganization($organizationId);
        $orgInfo            = $this->orgManager->getOrganization($organizationId);
        $transaction        = $this->mergeTransaction($data);
        $transactionType    = $this->getTransactionName($transaction);
        $recipientRegion    = $this->mergeRecipientRegion($data);
        $recipientCountry   = $this->mergeRecipientCountry($data);
        $sector             = $this->mergeSector($data);
        $activityStatus     = $this->mergeActivityStatus($data);
        $activityStatusJson = $this->convertIntoFormat($activityStatus);
        $activityName       = $this->getActivityName($data);

        $final_data = $this->getDataMerge(
            $transactionType,
            $recipientRegion,
            $recipientCountry,
            $sector,
            $activityName,
            $activityStatusJson
        );

        $user = $this->user->getDataByOrgIdAndRoleId($organizationId, '1');

        return view('who-is-using-organization', compact('final_data', 'orgInfo', 'user'));

    }

    /**
     * @param $data
     * @return array
     */
    protected function mergeTransaction($data)
    {
        $arrays = [];
        foreach ($data as $key => $datum) {
            foreach ($datum->activity_data['transaction'] as $index => $value) {
                if (array_key_exists($index, $arrays)) {
                    $arrays[$index] = $arrays[$index] + $value;
                } else {
                    $arrays[$index] = $value;
                }
            }
        }

        return $arrays;
    }

    /**
     * @param $data
     * @return array
     */
    protected function mergeRecipientRegion($data)
    {
        $arrays = [];
        foreach ($data as $key => $datum) {
            foreach ($datum->activity_data['recipient_region'] as $index => $value) {
                if (array_key_exists($index, $arrays)) {
                    $arrays[$index] = $arrays[$index] + $value;
                } else {
                    $arrays[$index] = $value;
                }
            }
        }

        return $arrays;
    }

    /**
     * @param $data
     * @return array
     */
    protected function mergeRecipientCountry($data)
    {
        $arrays = [];
        foreach ($data as $key => $datum) {
            foreach ($datum->activity_data['recipient_country'] as $index => $value) {
                if (array_key_exists($index, $arrays)) {
                    $arrays[$index] = $arrays[$index] + $value;
                } else {
                    $arrays[$index] = $value;
                }
            }
        }

        return $arrays;
    }

    /**
     * @param $data
     * @return array
     */
    protected function mergeSector($data)
    {
        $arrays = [];
        foreach ($data as $key => $datum) {
            foreach ($datum->activity_data['sector'] as $index => $value) {
                if (array_key_exists($index, $arrays)) {
                    $arrays[$index] = $arrays[$index] + $value;
                } else {
                    $arrays[$index] = $value;
                }
            }
        }

        return $arrays;
    }

    /**
     * @param $data
     * @return array
     */
    protected function mergeActivityStatus($data)
    {
        $helper = app()->make(GetCodeName::class);

        $arrays = [];
        foreach ($data as $key => $datum) {
            $index = $helper->getCodeName('Activity', 'ActivityStatus', $datum->activity_data['activity_status']);
            if ($index != null) {
                if (array_key_exists($index, $arrays)) {
                    $arrays[$index] = $arrays[$index] + 1;
                } else {
                    $arrays[$index] = 1;
                }
            }
        }

        return $arrays;
    }

    /**
     * @param $data
     * @return array
     */
    protected function getActivityName($data)
    {
        $arrays = [];

        foreach ($data as $key => $datum) {
            $arrays['title'][]      = $datum->activity_data['title'];
            $arrays['identifier'][] = $datum->activity_data['identifier'];
        }

        return $arrays;
    }

    protected function convertIntoFormat($data)
    {
        $arrays = [];
        foreach ($data as $key => $datum) {
            $arrays[] = [
                'region' => $key,
                'values' => $datum
            ];
        }

        return $arrays;
    }

    /**
     * @param $transaction
     * @return array
     */
    protected function getTransactionName($transaction)
    {
        $arrays = [
            'incomingFunds' => 0,
            'commitment'    => 0,
            'disbursement'  => 0,
            'expenditure'   => 0
        ];

        foreach ($transaction as $key => $value) {
            if ($key == 1) {
                $arrays['incomingFunds'] += (float) $value;
            } elseif ($key == 2) {
                $arrays['commitment'] += (float) $value;
            } elseif ($key == 3) {
                $arrays['disbursement'] += (float) $value;
            } elseif ($key == 4) {
                $arrays['expenditure'] += (float) $value;
            }
        }

        return $arrays;
    }

    /**
     * @param $transaction
     * @param $recipientRegion
     * @param $recipientCountry
     * @param $sector
     * @param $activityName
     * @param $activityStatus
     * @return array
     */
    protected function getDataMerge($transaction, $recipientRegion, $recipientCountry, $sector, $activityName, $activityStatus)
    {
        return [
            'transaction'       => $transaction,
            'recipient_region'  => $recipientRegion,
            'recipient_country' => $recipientCountry,
            'sector'            => $sector,
            'activity_status'   => $activityStatus,
            'activity_name'     => $activityName
        ];
    }
}
