<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use App\Model\Role;
use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        if ($user = User::where('user_name', $request->user_name)->first()) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                return redirect()->to('/');
            }
            $error = '密码错误';
        } else {
            $error = '用户名不存在';
        }
        $request->flashOnly('user_name');
        return view('auth.login')->withErrors(['user_name'=>$error]);
    }
    public function getLogout()
    {
        Auth::logout();
        return redirect()->to('/login');
    }

    public function getRegister()
    {
        $roles = Role::get();
        return view('auth.register', ['roles'=>$roles]);
    }

    public function postRegister(Request $request)
    {
        if ($request->password !== $request->password_confirmation) {
            return view('auth.register', ['message'=>'错误：两次密码不一致！']);
        }
        $username = $request->user_name;
        if (User::where('user_name', $username)->count() > 0) {
            return view('auth.register', ['message'=>'错误：用户名已存在！']);
        }
        $id = User::insertGetId([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ]);

        DB::table('role_user')->insert([
            'role_id'=>(int)$request->role_id,
            'user_id'=>$id
        ]);
        /*        User::create([
                    'user_name'=>$username,
                    'password'=>bcrypt($request->password)
                ]);*/
        $roles = Role::get();
        return view('auth.register', ['roles'=>$roles,'message'=>'注册成功！']);
    }
}
