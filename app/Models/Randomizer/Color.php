<?php

namespace App\Models\Randomizer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kiryanov\Filter\Filter\Filterable;

class Color extends Model
{
    const TYPE_COLOR = 0;
    const TYPE_DESIGN = 1;

    use HasFactory, Filterable;

    protected $fillable = [
        'color_name',
        'image',
        'type',
    ];

    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'color_tag');
    }
}
