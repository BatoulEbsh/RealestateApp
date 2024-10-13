<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class House extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'number_of_rooms',
        'number_of_bathroom',
        'number_of_balcony',
        'description',
        'direction',
        'property_id',
    ];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
