<?php namespace App\Services\Activity;

use App\Core\Version;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Database\DatabaseManager;
use Psr\Log\LoggerInterface as Logger;
use Exception;

/**
 * Class UploadActivityManager
 * @package App\Services\Activity
 */
class UploadActivityManager
{
    protected $auth;
    protected $version;
    protected $dbLogger;
    protected $logger;
    protected $activityRepo;
    protected $uploadActivityRepo;
    /**
     * @var DatabaseManager
     */
    protected $database;

    /**
     * @param Version         $version
     * @param Guard           $auth
     * @param DatabaseManager $database
     * @param Log             $dbLogger
     * @param Logger          $logger
     */
    public function __construct(Version $version, Guard $auth, DatabaseManager $database, Log $dbLogger, Logger $logger)
    {
        $this->auth               = $auth;
        $this->version            = $version;
        $this->dbLogger           = $dbLogger;
        $this->logger             = $logger;
        $this->database           = $database;
        $this->uploadActivityRepo = $version->getActivityElement()->getUploadActivity()->getRepository();
    }

    /**
     * upload activity form csv
     * @param $activityCsv
     * @param $organization
     * @return bool
     */
    public function save($activityCsv, $organization)
    {
        try {
            $excel          = $this->version->getExcel();
            $activitiesRows = $excel->load($activityCsv)->get();

            foreach ($activitiesRows as $activityRow) {
                $activityDetails[] = $this->uploadActivityRepo->formatFromExcelRow($activityRow, $organization->id);
            }

            $identifiers = $this->uploadActivityRepo->getIdentifiers($organization->id);
            $this->database->beginTransaction();
            $data = [];

            foreach ($activityDetails as $activityDetail) {
                $activityIdentifier = $activityDetail['identifier']['activity_identifier'];
                (isset($identifiers[$activityIdentifier]))
                    ? $data[$identifiers[$activityIdentifier]] = $activityDetail
                    : $this->uploadActivityRepo->upload($activityDetail, $organization);
                $this->database->commit();
            }

            if (count($data) > 0) {
                return view('Activity.confirmUpdate')->withData($data);
            }

            $this->logger->info("Activities Uploaded for organization with id:" . $organization->id);
            $this->dbLogger->activity("activity.activity_uploaded", ['organization_id' => $organization->id]);

            return true;
        } catch (Exception $exception) {
            $this->database->rollback();
            $this->logger->error(
                sprintf('Activity could not be uploaded due to %s', $exception->getMessage()),
                [
                    'activity' => 'activityDetails',
                    'trace'    => $exception->getTraceAsString()
                ]
            );
        }

        return false;
    }

    /**
     * update activities if the identifier exists
     * @param $activityDetails
     */
    public function update($activityDetails)
    {
        foreach ($activityDetails as $key => $activityDetail) {
            $activityDetail = json_decode($activityDetail, true);
            $this->uploadActivityRepo->update($activityDetail, $key);
        }
    }
}
