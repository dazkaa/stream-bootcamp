<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(){

        $relations = [
            'package',
            'user', //nama method dari model
        ];

        $transaction = Transaction::with($relations)->get(); //mengambil seluruh data

        return view('admin.transactions', ['transactions' => $transaction]); //merender data controller ke view
        // dd($transaction);
    }
}
