<?php

namespace App\DataAdapter;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Kiryanov\Adapter\DataAdapter\DataAdapter;

class ProductAdapter extends DataAdapter
{
    /**
     * @param Product $product
     */
    public function getModelData(Model $product) : array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'image' => $product->image,
            'price' => $product->price,
        ];
    }
}
