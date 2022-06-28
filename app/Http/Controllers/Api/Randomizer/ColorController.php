<?php

namespace App\Http\Controllers\Api\Randomizer;

use App\Filter\Randomizer\ColorFilter;
use App\Http\Controllers\Controller;
use App\Models\Randomizer\Color;
use Kiryanov\Adapter\DataAdapter\Randomizer\ColorAdapter;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{


    public function getRandomColor(ColorFilter $filter, ColorAdapter $adapter)
    {

        $random_color = Color::filter($filter)->orderBy(DB::raw('RAND()'))->take(1)->first();
        return $adapter->getModelData($random_color);

    }
}
