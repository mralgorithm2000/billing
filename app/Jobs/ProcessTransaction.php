<?php

namespace App\Jobs;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\TransactionRepository;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ProcessTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $amount;
    protected $type;

    public function __construct($user_id, $amount, $type)
    {
        $this->user_id = $user_id;
        $this->amount = $amount;
        $this->type = $type;
    }

    public function handle(TransactionRepositoryInterface $transactionRepository)
    {
        try {
            if ($this->type === 'deposit') {
                $transactionRepository->deposit($this->user_id, $this->amount);
            } elseif ($this->type === 'withdraw') {
                $transactionRepository->withdraw($this->user_id, $this->amount);
            }
        } catch (Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
