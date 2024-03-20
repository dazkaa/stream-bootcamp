<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index(){
        return view('member.register');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $data = $request->except('_token');

        $isEmailExist = User::where('email', $request->email)->exists(); //cek email apakah sudah terdaftar?

        if($isEmailExist) {
            return back()
                ->withErrors([
                    'email' => 'This email already registered' //create pesan error custom
                ])
                ->withInput(); //memunculkan inputan di form  
        }

        $data['password'] = Hash::make($request->password); //create format password hash
        $data['role'] = 'member'; //overwrite role menjadi member

        User::create($data);

        return redirect()->route('member.login');
    }
    
}
