<?php

namespace App\Http\Controllers\Constants;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Traits\ReturnResponse;

class PropertyTypeController extends Controller
{
    use ReturnResponse;
    public function index() {
        $propertyTypes = PropertyType::query()
            ->select('id')
            ->addSelect('name_' . request()->header('lang') . ' as name')
            ->get();
        return $this->returnData('propertyTypes', $propertyTypes);
    }
}
