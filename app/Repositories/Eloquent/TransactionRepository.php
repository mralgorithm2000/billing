<?php

namespace App\Repositories\Eloquent;

use App\Models\Activity;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\UserBalance;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var Transaction $transaction The transaction model instance.
     */
    private $transaction;

    /**
     * TransactionService constructor.
     *
     * @param Transaction $transaction The transaction model instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Processes a deposit transaction.
     *
     * @param int $user_id The ID of the user making the deposit.
     * @param float $amount The amount to deposit.
     * @return void
     * @throws \Exception If the transaction fails.
     */
    public function deposit($user_id, $amount)
    {
        DB::transaction(function () use ($user_id, $amount) {
            $balance = UserBalance::firstOrCreate(['user_id' => $user_id]);
            $balance->balance += $amount;
            $balance->save();

            $this->transaction->create([
                'user_id' => $user_id,
                'type' => 'deposit',
                'amount' => $amount,
                'description' => 'Deposit successful',
            ]);
        });
    }

    /**
     * Processes a withdrawal transaction.
     *
     * @param int $user_id The ID of the user making the withdrawal.
     * @param float $amount The amount to withdraw.
     * @return void
     * @throws \Exception If the user has insufficient balance.
     */
    public function withdraw($user_id, $amount)
    {
        DB::transaction(function () use ($user_id, $amount) {
            $balance = UserBalance::where('user_id', $user_id)->first();

            if (!$balance || $balance->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $balance->balance -= $amount;
            $balance->save();

            $this->transaction->create([
                'user_id' => $user_id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'description' => 'Withdrawal successful',
            ]);
        });
    }


    /**
     * Retrieve the transaction history for a specific user.
     * 
     * This method fetches the transaction records associated with the given user ID, and paginates the results.
     * 
     * @param int $user_id The ID of the user whose transaction history is being retrieved.
     * @param int $page_limit The number of transaction records to display per page.
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated list of transactions for the specified user.
     */
    public function history($user_id, $page_limit)
    {
        return Transaction::where('user_id', $user_id)
            ->paginate($page_limit)->makeHidden(['id','user_id']);
    }
}
