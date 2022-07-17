<?php

namespace App\Repository\Accounting;

use App\Models\Accounting\Transaction;

class TransactionRepository
{


    public function findByAuthUser()
    {
        $user = auth('api')->user();

        return Transaction::where(['user_id' => $user->id])->get();
    }
    public function findByAuthUserAndCategory(int $category_id)
    {
        $user = auth('api')->user();

        return Transaction::where(['user_id' => $user->id, 'category_id' => $category_id])->get();
    }
}
