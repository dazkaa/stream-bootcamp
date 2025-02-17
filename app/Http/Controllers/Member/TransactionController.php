<?php

namespace App\Http\Controllers\Member;

use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function store(Request $request){
        $package = Package::find($request->package_id);

        $transaction = Transaction::create([
            'package_id' => $package->id,
            'user_id' => auth()->user()->id,
            'amount' => $package->price,
            'transaction_code' => strtoupper(Str::random(10)),
            'status' => 'pending'
        ]);

        $customer = auth()->user();

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->code,
                'gross_amount' => $transaction->amount
            ],
            [
                'customer_details' => [
                    'first_name' => $customer->name,
                    'last_name' => $customer->name,
                    'email' => $customer->email,
                ]
            ]
        ];

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_IS_SANITIZED', true);
        \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_IS_3DS', true);

        $createMidTransaction = \Midtrans\Snap::createTransaction($params);
        $midtransRedirectUrl = $createMidTransaction->redirect_url;

        return redirect($midtransRedirectUrl);
    }
}
