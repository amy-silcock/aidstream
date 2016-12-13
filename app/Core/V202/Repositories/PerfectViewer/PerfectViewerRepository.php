<?php namespace App\Core\V202\Repositories\PerfectViewer;

use App\Models\Activity\Transaction;
use App\Models\ActivityPublished;
use App\Models\HistoricalExchangeRate;
use App\Models\Organization\Organization;
use App\Models\PerfectViewer\ActivitySnapshot;
use App\Models\PerfectViewer\OrganizationSnapshot;

/**
 * Class PerfectActivityViewerRepository
 * @package App\Core\V202\Repositories\PerfectViewer
 */
class PerfectViewerRepository
{

    /**
     * @var ActivitySnapshot
     */
    protected $activitySnapshot;

    /**
     * @var ActivityPublished
     */
    protected $activityPublished;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var Organization
     */
    protected $organization;

    /**
     * @var OrganizationSnapshot
     */
    protected $organizationSnapshot;

    /**
     * @var HistoricalExchangeRate
     */
    protected $historicalExchangeRate;

    /**
     * PerfectViewer constructor.
     * @param ActivitySnapshot       $activitySnapshot
     * @param ActivityPublished      $activityPublished
     * @param Transaction            $transaction
     * @param Organization           $organization
     * @param OrganizationSnapshot   $organizationSnapshot
     * @param HistoricalExchangeRate $historicalExchangeRate
     */
    public function __construct(
        ActivitySnapshot $activitySnapshot,
        ActivityPublished $activityPublished,
        Transaction $transaction,
        Organization $organization,
        OrganizationSnapshot $organizationSnapshot,
        HistoricalExchangeRate $historicalExchangeRate
    ) {
        $this->activitySnapshot     = $activitySnapshot;
        $this->activityPublished    = $activityPublished;
        $this->transaction          = $transaction;
        $this->organization         = $organization;
        $this->organizationSnapshot = $organizationSnapshot;
        $this->historicalExchangeRate = $historicalExchangeRate;
    }

    /**
     * Create new snapshot record or updates if record already exists
     * @param array $data
     * @return ActivitySnapshot
     */
    public function storeActivity(array $data)
    {
        return $this->activitySnapshot->updateOrCreate(['activity_id' => $data['activity_id'], 'org_id' => $data['org_id']], $data);
    }

    /**
     * @param array $perfectOrg
     * @return OrganizationSnapshot
     */
    public function storeOrganization(array $perfectOrg)
    {
        return $this->organizationSnapshot->updateOrCreate(['org_id' => $perfectOrg['org_id']], $perfectOrg);
    }

    /**
     * Provide Published Filename from organization id
     * @param $orgId
     * @return mixed
     */
    public function getPublishedFileName($orgId)
    {
        return $this->activityPublished->where('organization_id', $orgId)->orderBy('created_at', 'desc')->first(['filename']);
    }

    /**
     * Provide transaction data form activity id
     * @param $activityId
     * @return mixed
     */
    public function getTransactions($activityId)
    {
        $transactions = $this->transaction->where('activity_id', $activityId)->get();

        return $transactions->toArray();
    }

    /**
     * Provide organization data from organization id
     * @param $orgId
     * @return mixed
     */
    public function getOrganization($orgId)
    {
        return $this->organization->where('id', $orgId)->get();
    }

    /**
     * Provides all the organizations from Activity Snapshot
     * @return OrganizationSnapshot
     */
    public function organizationQueryBuilder()
    {
        return $this->organizationSnapshot->join('organizations', 'organizations.id', '=', 'organization_snapshots.org_id');
    }

    /**
     * @return ActivitySnapshot
     */
    public function activityQueryBuilder()
    {
        return $this->activitySnapshot;
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getSnapshot($orgId)
    {
        return $this->activitySnapshot->where('org_id', $orgId)->get();
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getOrgWithId($orgId)
    {
        return $this->organization->where('reporting_org[0].reporting_organization_identifier', $orgId)->get();
    }

    /**
     * @return HistoricalExchangeRate
     */
    public function getExchangeRatesBuilder()
    {
        return $this->historicalExchangeRate;
    }
}