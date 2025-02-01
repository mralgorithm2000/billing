<?php
namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface
{
    public function deposit($user_id,$amount);
    public function withdraw($user_id,$amount);
    public function history($user_id,$page_limit);
}