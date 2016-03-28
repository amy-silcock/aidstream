<?php namespace App\Services\Activity;

use App\Core\Version;
use App\Models\Activity\Activity;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactInfoManager
 * @package app\Services\Activity
 */
class ContactInfoManager
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
    protected $contactInfoRepo;

    /**
     * @param Version $version
     * @param Log     $log
     * @param Guard   $auth
     */
    public function __construct(Version $version, Log $log, Guard $auth)
    {
        $this->auth            = $auth;
        $this->log             = $log;
        $this->contactInfoRepo = $version->getActivityElement()->getContactInfo()->getRepository();
    }

    /**
     * updates Activity Contact Info
     * @param array    $activityDetails
     * @param Activity $activity
     * @return bool
     */
    public function update(array $activityDetails, Activity $activity)
    {
        try {
            $this->contactInfoRepo->update($activityDetails, $activity);
            $this->log->info(
                'Activity Contact Info Updated!',
                ['for ' => $activity['contact_info']]
            );
            $this->log->activity(
                "activity.contact_info_updated",
                [
                    'activity_id'     => $activity->id,
                    'organization'    => $this->auth->user()->organization->name,
                    'organization_id' => $this->auth->user()->organization->id
                ]
            );

            return true;
        } catch (Exception $exception) {
            $this->log->error(
                sprintf('Activity contact info could not be updated due to %s', $exception->getMessage()),
                [
                    'ContactInfo' => $activityDetails,
                    'trace'       => $exception->getTraceAsString()
                ]
            );
        }

        return false;
    }

    /**
     * @param $id
     * @return Model
     */
    public function getContactInfoData($id)
    {
        return $this->contactInfoRepo->getContactInfoData($id);
    }
}
