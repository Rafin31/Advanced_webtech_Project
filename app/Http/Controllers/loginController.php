<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\loginModel;
use App\Models\usersModel;
use App\Models\requestsModel;

class loginController extends Controller
{
    public function index()
    {
        return view("login.login");
    }
    public function logout(Request $req)
    {
        $req->session()->flush();
        return redirect("/login");
    }
    public function loginVarify(loginForm $req)
    {
        $user_name = $req->user_name;
        $password = bcrypt($req->password);

        $user = loginModel::where('user_name', $user_name)
            ->first();

        //checking users
        if ($user) {
            //checking account status
            if ($user['account_Status'] == 'pending') {


            $req->session()->put('status', true);
            $req->session()->put('user_name', $req->user_name);
            $req->session()->put('user_id', $user['id']);
            $req->session()->put('user_type', $user['user_type']);
            if($user['user_type'] == 'admin'){
                return redirect()->route('user.dashbord'); //admin
            }
           
            elseif($user['user_type'] == 'clients'){
                return redirect()->route('client.index'); //client

                $req->session()->flash('msg', 'Your account is in pending');
                return redirect()->route('login.login');
            } elseif ($user['account_Status'] == 'Block') {

                $req->session()->flash('msg', 'Your account is Blocked');
                return redirect()->route('login.login');
            } else {

                if (Hash::check($req->password, $user['password'])) {
                    if ($user['user_type'] == 'admin') {
                        $req->session()->put('status', true);
                        $req->session()->put('user_name', $req->user_name);
                        $req->session()->put('user_id', $user['id']);
                        $req->session()->put('user_type', $user['user_type']);
                        return redirect()->route('user.dashbord');
                    } elseif ($user['user_type'] == 'clients') {
                        // client
                        $req->session()->put('status', true);
                        $req->session()->put('user_name', $req->user_name);
                        $req->session()->put('user_id', $user['id']);
                        $req->session()->put('user_type', $user['user_type']);
                        return redirect()->route('client.index');
                    } elseif ($user['user_type'] == 'bank_manager') {
                        //code
                    } elseif ($user['user_type'] == 'noney_exchange_officer') {
                        //code
                    } else {
                        $req->session()->flash('msg', 'invaild request');
                        return redirect()->route('login.login');
                    }
                } else {
                    $req->session()->flash('msg', 'invaild User Name or password');
                    return redirect()->route('login.login');
                }

            }
        } else {
            $req->session()->flash('msg', 'invaild User Name or password');
            return redirect()->route('login.login');
        }
    }
    public function dashbord()
    {
        return view("user.index");
    }
    public function signUP()
    {
        return view('registration.register');
    }
}
