<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Property;
use App\Models\User;
use App\Traits\Helper;
use App\Traits\ReturnResponse;
use Illuminate\Http\Request;


class FavouriteController extends Controller
{
    use Helper, ReturnResponse;

    public function add($id)
    {
        $property = Property::query()->find($id);
        if (!$property) {
            return $this->returnError(422, 'property not found');
        }
        $user = auth()->user();
        if (!$user->favourite) {
            $favourite = new Favourite();
            $favourite->fill([
                'user_id' => $user->id
            ]);
            $favourite->save();
        } else {
            $favourite = $user->favourite;
        }
        $favourite->properties()->syncWithoutDetaching([$property->id]);
        return $this->returnSuccessMessage('successfully');
    }

    public function delete($id)
    {
        $property = Property::query()->find($id);
        if (!$property) {
            return $this->returnError(422, 'property not found');
        }
        $user = auth()->user();
        if (!$user->favourite) {
            $favourite = new Favourite();
            $favourite->fill([
                'user_id' => $user->id
            ]);
            $favourite->save();
        } else {
            $favourite = $user->favourite;
        }
        $favourite->properties()->detach([$property->id]);
        return $this->returnSuccessMessage('successfully');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->favourite) {
            return $this->returnData('properties', null);
        } else {
            $favourite = $user->favourite;
            $q = $favourite->properties()
                ->select([
                    'properties.id',
                    'price',
                    'space',
                    'PS.name_' . $request->header('lang') . ' as state',
                    'G.name_' . $request->header('lang') . ' as governorate',
                    'R.name_' . $request->header('lang') . ' as region',
                    'PT.name_' . $request->header('lang') . ' as type',
                ])
                ->with('images')
                ->join('property_states as PS', 'properties.property_state_id', '=', 'PS.id')
                ->join('property_types as PT', 'properties.property_type_id', '=', 'PT.id')
                ->join('regions as R', 'properties.region_id', '=', 'R.id')
                ->join('governorates as G', 'R.governorate_id', '=', 'G.id')
                ->get();
            return $this->returnData('properties', $q);
        }
    }
}
