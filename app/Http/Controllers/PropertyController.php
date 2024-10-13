<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\House;
use App\Models\Market;
use App\Models\Property;
use App\Traits\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ReturnResponse;
use function PHPUnit\Framework\exactly;


class PropertyController extends Controller
{
    use ReturnResponse, Helper;

    public function index(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'x' => 'required|numeric|min:1',
            'y' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        $q = $this->returnProperty($request, Property::query())
            ->selectRaw('SQRT(POW(properties.x - ' . $input['x'] . ', 2) + POW(properties.y - ' . $input['y'] . ', 2)) as distance')
            ->orderBy('distance')
            ->paginate($request->input('per_page', 10));
        $qData = $q->items();
        $q = [
            'current_page' => $q->currentPage(),
            'data' => $qData,
            'from' => $q->firstItem(),
            'last_page' => $q->lastPage(),
            'per_page' => $q->perPage(),
            'to' => $q->lastItem(),
            'total' => $q->total(),
        ];
        return $this->returnData('properties', $q);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'price' => 'required|numeric|min:1',
            'space' => 'required|numeric|min:1',
            'region_id' => 'required|exists:regions,id',
            'x' => 'required|numeric|min:1',
            'y' => 'required|numeric|min:1',
            'property_type_id' => 'required|exists:property_types,id'
//            'images' => 'required|array',
//            'images.*' => 'image'
        ]);
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        if ($input['property_type_id'] == 1) {
            $validator = Validator::make($input, [
                'number_of_rooms' => 'required|integer',
                'number_of_bathroom' => 'required|integer',
                'number_of_balcony' => 'required|integer',
                'description' => 'required|string',
                'direction' => 'required|string'
            ]);
        } elseif ($input['property_type_id'] == 2) {
            $validator = Validator::make($input, [
                'number_of_rooms' => 'required|integer',
                'number_of_pools' => 'required|integer',
                'is_garden' => 'required|boolean',
                'is_bar' => 'required|boolean',
                'is_baby_pool' => 'required|boolean',
                'description' => 'required|string'
            ]);
        } else {
            $validator = Validator::make($input, [
                'description' => 'required|string'
            ]);
        }
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        $property = new Property();
        $user_id = Auth::id();
        $property->fill(
            [
                'price' => $input['price'],
                'space' => $input['space'],
                'user_id' => $user_id,
                'region_id' => $input['region_id'],
                'x' => $input['x'],
                'y' => $input['y'],
                'property_type_id' => $input['property_type_id'],
                'property_state_id' => 5 //processing
            ]
        );
        $property->save();
        if ($input['property_type_id'] == 1) {
            $house = new House();
            $house->fill([
                'description' => $input['description'],
                'property_id' => $property->id,
                'number_of_balcony' => $input['number_of_balcony'],
                'number_of_rooms' => $input['number_of_rooms'],
                'number_of_bathroom' => $input['number_of_bathroom'],
                'direction' => $input['direction']
            ]);
            $house->save();
        } elseif ($input['property_type_id'] == 2) {
            $farm = new Farm();
            $farm->fill([
                'description' => $input['description'],
                'property_id' => $property->id,
                'number_of_rooms' => $input['number_of_rooms'],
                'number_of_pools' => $input['number_of_pools'],
                'is_bar' => $input['is_bar'],
                'is_baby_pool' => $input['is_baby_pool'],
                'is_garden' => $input['is_garden']
            ]);
            $farm->save();
        } else {
            $market = new Market();
            $market->fill([
                'description' => $input['description'],
                'property_id' => $property->id,
            ]);
            $market->save();
        }
//        $this->saveImages($input['images'], $property->id, "Property $property->id");
        return $this->returnData('property', $property, 'property added successfully');

    }

    public function show($id,Request $request)
    {
        $property =Property::query()
            ->select([
            'properties.id',
            'price',
            'space',
            'user_id',
            'region_id',
            'properties.created_at',
            'property_state_id',
            'PS.name_' . $request->header('lang') . ' as state',
            'G.name_' . $request->header('lang') . ' as governorate',
            'R.name_' . $request->header('lang') . ' as region',
            'PT.name_' . $request->header('lang') . ' as type',
            'properties.x',
            'properties.y',
        ])
            ->with(['images', 'farm', 'house', 'market'])
            ->join('property_states as PS', 'properties.property_state_id', '=', 'PS.id')
            ->join('property_types as PT', 'properties.property_type_id', '=', 'PT.id')
            ->join('regions as R', 'properties.region_id', '=', 'R.id')
            ->join('governorates as G', 'R.governorate_id', '=', 'G.id')
            ->find($id);
        return $this->returnData('property',$property);
    }

    public function update(Request $request, $id)
    {

    }


    public function showAll(Request $request)
    {
        return $this->returnData('properties', $this->returnProperty($request, Property::query())->get());
    }

    public function destroy($id)
    {
        $property = Property::find($id);
        if (!$property)
            return $this->returnError(422, 'Property not found');
        if ($property->property_state_id == 4)
            return $this->returnError(401, 'Property already deleted');
        $property->property_state_id = 4;
        $property->save();
        return $this->returnSuccessMessage('successfully');
    }

    public function search(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'price' => 'numeric|min:1',
            'space' => 'numeric|min:1',
            'region_id' => 'exists:regions,id',
            'governorate_id' => 'exists:governorates,id'
        ]);
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        $q = $this->returnProperty($request, Property::query());
        if (isset($input['price']))
            $q->where('price', '=', $input['price']);
        if (isset($input['space']))
            $q->where('space', '=', $input['space']);
        if (isset($input['region_id']))
            $q->where('region_id', '=', $input['region_id']);
        if (isset($input['governorate_id']))
            $q->where('governorate_id', '=', $input['governorate_id']);
        return $this->returnData('properties', $q->get());
    }

    public function returnProperty(Request $request, Builder $q): Builder
    {
        return $q
            ->select([
                'properties.id',
                'price',
                'space',
                'PS.name_' . $request->header('lang') . ' as state',
                'G.name_' . $request->header('lang') . ' as governorate',
                'R.name_' . $request->header('lang') . ' as region',
                'PT.name_' . $request->header('lang') . ' as type',
                'properties.x',
                'properties.y',
            ])
            ->where('property_state_id', '=', 1)
            ->with('images')
            ->join('property_states as PS', 'properties.property_state_id', '=', 'PS.id')
            ->join('property_types as PT', 'properties.property_type_id', '=', 'PT.id')
            ->join('regions as R', 'properties.region_id', '=', 'R.id')
            ->join('governorates as G', 'R.governorate_id', '=', 'G.id');
    }
}
