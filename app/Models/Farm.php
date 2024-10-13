<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farm extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'number_of_rooms',
        'number_of_pools',
        'is_garden',
        'is_bar',
        'is_baby_pool',
        'description',
        'property_id',
    ];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    protected $casts = [
        'is_garden' => 'boolean',
        'is_bar' => 'boolean',
        'is_baby_pool' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
