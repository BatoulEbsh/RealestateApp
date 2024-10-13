<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletOperation;
use App\Traits\Helper;
use App\Traits\ReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use ReturnResponse, Helper;

    public function deposit(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|exists:users,email',
            'value' => 'required|numeric|min:1'
        ]);
        if ($validator->fails()) {
            // 422 status code is for validation data errors
            return $this->returnError(422, $validator->errors());
        }
        $me = auth()->user();
        if ($input['email'] == $me->email) {
            return $this->returnError(401, 'Error');
        }
        $user = User::where('email', '=', $input['email'])->first();
        $wallet = $user->wallet;
        $deposit = new WalletOperation();
        $deposit->fill([
            'wallet_id' => $wallet->id,
            'type' => true,
            'value' => $input['value'],
            'description' => 'Deposit'
        ]);
        $deposit->save();
        return $this->returnSuccessMessage("Successfully");
    }

    public function withdrawAmount(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|exists:users,email',
            'value' => 'required|numeric|min:1'
        ]);
        if ($validator->fails()) {
            // 422 status code is for validation data errors
            return $this->returnError(422, $validator->errors());
        }
        $me = auth()->user();
        if ($input['email'] == $me->email) {
            return $this->returnError(401, 'Error');
        }
        $user = User::where('email', '=', $input['email'])->first();
        $wallet = $user->wallet;
        $withdrawAmount = new WalletOperation();
        $withdrawAmount->fill([
            'wallet_id' => $wallet->id,
            'type' => false,
            'value' => $input['value'],
            'description' => 'Withdraw an amount'
        ]);
        $withdrawAmount->save();
        return $this->returnSuccessMessage("Successfully");
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $q = Wallet::query()
            ->select(['id'])
            ->addSelect(['imports' => WalletOperation::query()
                ->selectRaw("SUM(value)")
                ->where('type', '=', true)
                ->whereColumn('wallet_id', '=', 'wallets.id')
            ])
            ->addSelect(['exports' => WalletOperation::query()
                ->selectRaw("SUM(value)")
                ->where('type', '=', false)
                ->whereColumn('wallet_id', '=', 'wallets.id')
            ])
            ->where('user_id', '=', $userId)
            ->first();

        $q['total'] = $q['imports'] - $q['exports'];
        $allFinancialOperations = $q->walletOperations()
            ->paginate($request->input('per_page', 10));
        $allFinancialOperationsData = $allFinancialOperations->items();
        $q['walletOperations'] = [
            'current_page' => $allFinancialOperations->currentPage(),
            'data' => $allFinancialOperationsData,
            'from' => $allFinancialOperations->firstItem(),
            'last_page' => $allFinancialOperations->lastPage(),
            'per_page' => $allFinancialOperations->perPage(),
            'to' => $allFinancialOperations->lastItem(),
            'total' => $allFinancialOperations->total(),
        ];

        return $this->returnData('wallet', $q);
    }

}
