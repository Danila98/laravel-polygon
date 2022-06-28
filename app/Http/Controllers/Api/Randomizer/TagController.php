<?php

namespace App\Http\Controllers\Api\Randomizer;

use App\Http\Controllers\Controller;
use App\Models\Randomizer\Tag;
use Kiryanov\Adapter\DataAdapter\Randomizer\TagAdapter;

class TagController extends Controller
{
    public function getTags(TagAdapter $adapter)
    {
        return $adapter->getArrayModelData(Tag::all());
    }
}
