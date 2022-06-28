<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kiryanov\Filter\Filter\Filterable;

class Product extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'id',
        'name',
        'img',
        'price',
    ];
}
