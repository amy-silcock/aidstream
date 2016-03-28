<?php namespace App\Services\Activity;

use App\Core\Version;
use App\Models\Activity\Activity;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityScope
 * @package App\Services\Activity
 */
class ActivityScopeManager
{
    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var Log
     */
    protected $log;

    /**
     * @var Version
     */
    protected $version;
    protected $activityScopeRepo;

    /**
     * @param Version $version
     * @param Log     $log
     * @param Guard   $auth
     */
    function __construct(Version $version, Log $log, Guard $auth)
    {
        $this->activityScopeRepo = $version->getActivityElement()->getActivityScope()->getRepository();
        $this->auth              = $auth;
        $this->log               = $log;
    }

    /**
     * Update Activity Status
     * @param array    $input
     * @param Activity $activity
     * @return bool
     */
    public function update(array $input, Activity $activity)
    {
        try {
            $this->activityScopeRepo->update($input, $activity);
            $this->log->info(
                'Activity Status  Updated!',
                ['for ' => $activity['activity_scope']]
            );
            $this->log->activity(
                "activity.activity_scope_updated",
                [
                    'activity_id'     => $activity->id,
                    'organization'    => $this->auth->user()->organization->name,
                    'organization_id' => $this->auth->user()->organization->id
                ]
            );

            return true;
        } catch (Exception $exception) {
            $this->log->error(
                sprintf('Activity Scope could not be updated due to %s', $exception->getMessage()),
                [
                    'ActivityScope' => $input,
                    'trace'         => $exception->getTraceAsString()
                ]
            );
        }

        return false;
    }

    /**
     * Get the Activity Scope data for Activity with the given id.
     * @param $id
     * @return Model
     */
    public function getActivityScopeData($id)
    {
        return $this->activityScopeRepo->getActivityScopeData($id);
    }
}
