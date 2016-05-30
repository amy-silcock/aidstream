<?php namespace App\Tz\Aidstream\Repositories\Transaction;

use App\Tz\Aidstream\Models\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Class TransactionRepository
 * @package App\Tz\Aidstream\Repositories\Transaction
 */
class TransactionRepository implements TransactionRepositoryInterface
{

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * TransactionRepository constructor.
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get transaction data via activity id
     * @param $activityId
     * @return mixed
     */
    public function findByActivityId($activityId)
    {
        return $this->transaction->where('activity_id', '=', $activityId)->get();
    }

    /**
     * Select data on basis of transactions type and project id
     * @param $projectId
     * @param $transactionType
     * @return mixed
     */
    public function getTransactionTypeData($projectId, $transactionType)
    {
        $transaction = DB::select("select * from activity_transactions where activity_id = ? and transaction #>> '{transaction_type,0,transaction_type_code}' = ?", [$projectId, $transactionType]);

        return $transaction;
    }

    /**
     * Save data into database
     * @param $transactions
     * @return bool
     */
    public function create($transactions)
    {
        foreach ($transactions['transaction'] as $transaction) {
            $transactionData = $this->transaction->newInstance(['transaction' => $transaction, 'activity_id' => $transactions['project_id']]);
            $transactionData->save();
        }

        return true;
    }

    /**
     * Update Transactions
     * @param $transactions
     * @return bool
     */
    public function update($transactions)
    {
        foreach ($transactions['transaction'] as $transactionData) {
            $transaction = $this->transaction->find($transactionData['id']);
            unset($transactionData['id']);
            $transactionData = [
                'transaction' => $transactionData
            ];
            $transaction->update($transactionData);
        }

        return true;
    }

}
