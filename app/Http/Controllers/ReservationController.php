<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\WalletOperation;
use App\Traits\Helper;
use App\Traits\ReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    use ReturnResponse, Helper;

    public function index()
    {

    }

    public function accept($id)
    {
        $reservation = Reservation::find($id);
        $property = $reservation->property;
        $u1 = $this->howMoney($reservation->user);
        if ((5 * 100) / $reservation->price > $u1) {
            return 'asas';
        }
        if ($property->user_id != auth()->id()) {
            return ';;dd';
        }
        if ($reservation->reservation_type_id == 1) {
            $property->property_state_id = 3;
            $property->save();
        } else {
            if ($reservation->start_date >= now()->format('Y-m-d')) {
                $property->property_state_id = 2;
                $property->save();
            }
        }

        $wallet1 = $reservation->user->wallet;
        $o1 = new WalletOperation();
        $o1->fill(
            [
                'type' => false,
                'value' => (5 * 100) / $reservation->price,
                'wallet_id' => $wallet1->id
            ]
        );
        $o1->save();
        $wallet2 = $property->user->wallet;
        $o2 = new WalletOperation();
        $o2->fill(
            [
                'type' => true,
                'value' => (5 * 100) / $reservation->price,
                'wallet_id' => $wallet2->id
            ]
        );
        $o2->save();
        $reservation->reservation_state_id = 2;
        $reservation->save();
        return $this->returnSuccessMessage('reservation accepted');
    }

    public function reject()
    {

    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'date|after:start_date',
            'price' => 'required|numeric|min:1',
            'reservation_type_id' => 'required|exists:reservation_types,id'
        ]);
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        $property = Property::find($input['property_id']);
        if ($property->property_state_id != 1) {
            return $this->returnError(401, 'property not available');
        }
        if ($input['reservation_type_id'] == 1) {
            if ($input['price'] < $property->price) {
                return $this->returnError(401, 'property price is low');
            }
        }
        if (Reservation::query()
            ->where('property_id', '=', $input['property_id'])
            ->where('reservation_state_id', '=', 2)
            ->whereDate('start_date', '<=', $input['start_date'])
            ->whereDate('end_date', '>=', $input['end_date'] ?? now()->addYears(100))
            ->exists()) {
            return $this->returnError(401, 'reservation is error');
        }
        if (auth()->id() == $property->user_id) {
            return $this->returnError(401, 'nnn');
        }
        if ($input['reservation_type_id'] != 1) {
            if (!isset($input['end_date'])) {
                return $this->returnError(401, 'bbb');
            }
        }
        $reservation = new Reservation();
        $reservation->fill(
            [
                'property_id' => $input['property_id'],
                'price' => $input['price'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'] ?? null,
                'reservation_type_id' => $input['reservation_type_id'],
                'user_id' => auth()->id(),
                'reservation_state_id' => 1
            ]
        );
        $reservation->save();
        return $this->returnSuccessMessage('reservation added successfully');
    }

    public function show($id)
    {
    }


    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
