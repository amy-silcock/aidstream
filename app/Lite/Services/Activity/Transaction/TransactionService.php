<?php namespace App\Lite\Services\Activity\Transaction;

use App\Lite\Services\Data\Traits\TransformsData;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use App\Models\Activity\Transaction;
use Exception;
use Illuminate\Database\DatabaseManager;
use Psr\Log\LoggerInterface;
use App\Lite\Contracts\TransactionRepositoryInterface;

/**
 * Class TransactionService
 * @package app\Lite\Services\Transaction
 */
class TransactionService
{
    use ProvidesLoggerContext, TransformsData;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * Disbursement type code
     */
    const DISBURSEMENT = 3;
    /**
     * Expenditure type code
     */
    const EXPENDITURE = 4;
    /**
     * Incoming funds type code
     */
    const INCOMING_FUNDS = 1;

    /**
     * TransactionService constructor.
     *
     * @param DatabaseManager                $databaseManager
     * @param TransactionRepositoryInterface $transactionRepository
     * @param LoggerInterface                $logger
     */
    public function __construct(DatabaseManager $databaseManager, TransactionRepositoryInterface $transactionRepository, LoggerInterface $logger)
    {
        $this->transactionRepository = $transactionRepository;
        $this->logger                = $logger;
        $this->databaseManager       = $databaseManager;
    }

    /**
     * Get all Transactions for the current Organization.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function all()
    {

    }

    /**
     *  Find a Specific Transaction.
     *
     * @param $id
     * @return Transaction
     */
    public function find($id)
    {
        return $this->transactionRepository->find($id);
    }

    /**
     * Returns Budget Model in view format
     *
     * @param $activityId
     * @param $type
     * @param $version
     * @return array
     * @internal param $budget
     */
    public function getModel($activityId, $type, $version)
    {
        $model = json_decode($this->transactionRepository->findByActivityId($activityId), true);

        $newModel = [];

        foreach ($model as $index => $value) {
            if (getVal($value, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '') == $type) {
                $newModel[] = $value;
            }
        }

        $filteredModel = $this->transformReverse($this->getMapping($newModel, 'Transaction', $version));

        return $filteredModel;
    }

    /**
     * Adds new disbursement transaction to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addDisbursement($activityId, $rawData, $version)
    {
        $rawData['type']        = self::DISBURSEMENT;
        $rawData['activity_id'] = $activityId;

        return $this->addTransaction($rawData, $version);
    }

    /**
     * Adds new expenditure transaction to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addExpenditure($activityId, $rawData, $version)
    {
        $rawData['type']        = self::EXPENDITURE;
        $rawData['activity_id'] = $activityId;

        return $this->addTransaction($rawData, $version);
    }

    /**
     * Adds new disbursement transaction to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addIncomingFunds($activityId, $rawData, $version)
    {
        $rawData['type']        = self::INCOMING_FUNDS;
        $rawData['activity_id'] = $activityId;

        return $this->addTransaction($rawData, $version);
    }

    /**
     * Deletes a transaction of current activity.
     *
     * @param $index
     * @return bool|null
     * @internal param $request
     */
    public function delete($index)
    {
        try {
            $transactions = $this->find($index);
            $this->databaseManager->beginTransaction();
            $transactions->delete();
            $this->databaseManager->commit();
            $this->logger->info('Transaction successfully deleted.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->databaseManager->rollback();
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Filters transaction according to their type
     *
     * @param $transactions
     * @return array
     */
    public function getFilteredTransactions($transactions)
    {
        $filteredTransactions = [];
        foreach ($transactions as $index => $transaction) {
            $type = getVal($transaction, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '');
            if ($type == self::DISBURSEMENT) {
                $filteredTransactions['disbursement'][] = $transaction;
            }
            if ($type == self::EXPENDITURE) {
                $filteredTransactions['expenditure'][] = $transaction;
            }
            if ($type == self::INCOMING_FUNDS) {
                $filteredTransactions['incoming'][] = $transaction;
            }
        }

        return $filteredTransactions;
    }

    /**
     * Provides default currency
     *
     * @param $activity
     * @return null|string
     */
    public function getDefaultCurrency($activity)
    {
        $settings     = $activity->organization->settings;
        $activityData = json_decode($activity, true);
        $settingsData = json_decode($settings, true);

        $activityCurrency = getVal($activityData, ['default_field_values', 0, 'default_currency'], '');
        if ($activityCurrency) {
            return $activityCurrency;
        }

        $settingsCurrency = getVal($settingsData, ['default_field_values', 0, 'default_currency'], '');
        if ($settingsCurrency) {
            return $settingsCurrency;
        }

        return null;
    }

    /**
     * Add new transaction
     *
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    protected function addTransaction($rawData, $version)
    {
        try {
            $mappedBudget = $this->transform($this->getMapping($rawData, 'Transaction', $version));

            $this->databaseManager->beginTransaction();
            foreach ($mappedBudget as $index => $value) {
                $this->transactionRepository->save($value);
            }
            $this->databaseManager->commit();

            $this->logger->info('Transaction successfully added.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->databaseManager->rollback();
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Returns Transaction type form code
     *
     * @param $type
     * @return string
     */
    public function getTransactionType($type)
    {
        if ($type == self::EXPENDITURE) {
            return 'Disbursement';
        }

        if ($type == self::EXPENDITURE) {
            return 'Expenditure';
        }

        if ($type == self::INCOMING_FUNDS) {
            return 'IncomingFunds';
        }

    }

    /**
     * Transactions are updated or created if new transaction
     *
     * @param $activityId
     * @param $type
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function updateOrCreate($activityId, $type, $rawData, $version)
    {
        try {
            $rawData['type']        = $type;
            $rawData['activity_id'] = $activityId;

            $this->databaseManager->beginTransaction();
            $this->updateOrCreateTransactions($this->transform($this->getMapping($rawData, 'Transaction', $version)));
            $this->databaseManager->commit();

            $this->logger->info('Transactions successfully updated.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->databaseManager->rollback();
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Update or Create Transactions
     *
     * @param array $transactions
     */
    protected function updateOrCreateTransactions(array $transactions)
    {
        foreach ($transactions as $index => $value) {
            (array_key_exists('id', $value)) ? $this->transactionRepository->update($value) : $this->transactionRepository->save($value);
        }
    }

}
