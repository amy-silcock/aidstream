<?php namespace App\Core\V201\Repositories\Activity;

use App\Models\Activity\Activity;

/**
 * Class ActivityRepository
 * @package app\Core\V201\Repositories\Activity
 */
class ActivityRepository
{
    protected $activity;

    /**
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * insert activity data to database
     * @param array $input
     * @param       $organizationId
     * @return modal
     */
    public function store(array $input, $organizationId)
    {
        unset($input['_token']);

        return $this->activity->create(
            [
                'identifier'      => $input,
                'organization_id' => $organizationId
            ]
        );
    }

    /**
     * @param $organizationId
     * @return modal
     */
    public function getActivities($organizationId)
    {
        return $this->activity->where('organization_id', $organizationId)->get();
    }

    /**
     * @param $activityId
     * @return model
     */
    public function getActivityData($activityId)
    {
        return $this->activity->findorFail($activityId);
    }

    /**
     * @param array    $input
     * @param Activity $activityData
     */
    public function updateStatus(array $input, Activity $activityData)
    {
        $activityData->activity_workflow = $input['activity_workflow'];
        $activityData->save();
    }

    /**
     * @param $activity_id
     */
    public function resetActivityWorkflow($activity_id)
    {
        $this->activity->whereId($activity_id)->update(['activity_workflow' => 0]);
    }
}
