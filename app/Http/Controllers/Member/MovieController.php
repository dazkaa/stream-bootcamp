<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\UserPremium;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MovieController extends Controller
{
    public function show($id){
        return view('member.movie-detail');
    }

    public function watch($id) {
        $userID = auth()->user()->id;
        
        $userPremium = UserPremium::where('user_id', $userID)->first();

        if($userPremium) {
            $endOfSubscription = $userPremium->end_of_subscription;
            $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription); //convert string menjadi objek carbon
            $isValidSubscription = $date->lessThan(now());

            if($isValidSubscription){
                return view('member.movie-watch');
            }
        }
        
        return redirect()->route('pricing');

    }
}
