<?php

namespace App\Repository\Accounting;

use App\Models\Accounting\Category;

class CategoryRepository
{
    public function getByUser($user)
    {
        return Category::where(['user_id' => $user->id])->get();
    }
}
