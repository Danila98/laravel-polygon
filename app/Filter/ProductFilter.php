<?php

namespace App\Filter;

use Kiryanov\Filter\Filter\QueryFilter;

class ProductFilter extends QueryFilter
{
    /**
     * @param string $name
     */
    public function name(string $name)
    {
        $this->builder->where('name','LIKE' , '%'.$name.'%');
    }
}
