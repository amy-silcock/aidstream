<?php namespace App\Services\Activity;

use App\Core\Version;
use App;
use App\Models\Activity\Activity;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Contracts\Logging\Log as DbLogger;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class TransactionManager
 * @package App\Services\Activity
 */
class TransactionManager
{
    /**
     * @var Guard
     */
    protected $auth;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var DbLogger
     */
    protected $dbLogger;
    /**
     * @var Version
     */
    protected $version;
    protected $transactionRepo;

    /**
     * @param Version  $version
     * @param Guard    $auth
     * @param DbLogger $dbLogger
     * @param Logger   $logger
     */
    public function __construct(Version $version, Guard $auth, DbLogger $dbLogger, Logger $logger)
    {

        $this->auth            = $auth;
        $this->logger          = $logger;
        $this->dbLogger        = $dbLogger;
        $this->transactionRepo = $version->getActivityElement()->getTransaction()->getRepository();
    }

    /**
     * saves the transaction details
     * @param array    $transactionDetails
     * @param Activity $activity
     * @param null     $transactionId
     * @return bool
     */
    public function save(array $transactionDetails, Activity $activity, $transactionId = null)
    {
        try {
            ($transactionId) ? $this->transactionRepo->update($transactionDetails, $transactionId) : $this->transactionRepo->create($transactionDetails, $activity);
            $this->logger->info(($transactionId) ? 'Activity Transaction Updated' : 'Activity Transaction added');
            $this->dbLogger->activity(
                ($transactionId) ? "transaction_updated" : "transaction_added",
                [
                    'activity_id'    => $activity->id,
                    'transaction_id' => $transactionId
                ]
            );

            return true;
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf(
                    'Transaction could not be %s due to %s',
                    ($transactionId) ? 'updated' : 'added',
                    $exception->getMessage()
                ),
                [
                    'transaction' => $transactionDetails,
                    'trace'       => $exception->getTraceAsString()
                ]
            );
        }

        return false;
    }

    /**
     * get transaction detail
     * @param $transactionId
     * @return mixed
     */
    public function getTransaction($transactionId)
    {
        return $this->transactionRepo->getTransaction($transactionId);
    }
}
