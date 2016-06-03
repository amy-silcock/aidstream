<?php namespace App\Tz\Aidstream\Repositories\Transaction;

/**
 * Interface TransactionRepositoryInterface
 * @package App\Tz\Aidstream\Repositories\Transaction
 */
interface TransactionRepositoryInterface
{

    /**
     * @param $activityId
     * @return mixed
     */
    public function findByActivityId($activityId);

    /**
     * Get data from db
     * @param $activityId
     * @param $transactionType
     * @return mixed
     */
    public function getTransactionTypeData($activityId, $transactionType);

    /**
     * Create Transactions
     * @param $transactions
     * @return mixed
     */
    public function create($transactions);

    /**
     * Update Transactions
     * @param $transactions
     * @return mixed
     */
    public function update($transactions);

    /**
     * Find transaction by Id
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Delete specific transaction
     * @param $transaction
     * @return mixed
     */
    public function destroy($transaction);

    public function findByType($projectId, $transactionType);
}