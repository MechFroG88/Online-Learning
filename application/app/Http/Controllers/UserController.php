<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Validator;	
use App\User;
use App\Mail\ResetPassword;
use Auth;
use DB;

class UserController extends Controller
{

    private $rules = [
        "username" => "required|unique:users",
        "cn_name"  => ["required","regex:/[\x{4e00}-\x{9fa5}]+/u"],
        "en_name"  => "required",
        "password" => "",
        "email"    => "required",
        "type"     => "required|integer|between:0,1",
    ];

    private $edit_rules = [
        "cn_name"  => "required",
        "en_name"  => "required",
        "password" => "",
        "email"    => "required",
        "type"     => "required|integer|between:0,1",
    ];


    private $login_rules = [
        "username" => "required",
        "password" => "required"
    ];

    private $forget_rules = [
        "username" => "required",
        "email"    => "required"
    ];

    private $reset_rules = [
        "username"     => "required",
        "old_password" => "required",
        "new_password" => "required|confirmed|different:old_password"
    ];

    public function hash()
    {
        $users = DB::table('users')->select('id','password')->get();
        foreach ($users as $user){
            DB::table('users')
            ->where('id',$user->id)
            ->update(['password' => Hash::make($user->password)]);
        }
        return $this->ok();
    }

    public function clear_cache()
    {
        Cache::flush();
    }

    public function login(Request $data)
    {
        $validator = Validator::make($data->all(), $this->login_rules);
        if ($validator->fails()) return $this->fail($validator);
        if (Auth::attempt(["username" => $data->username , "password" => $data->password],true)) {
            return $this->get_current();
        } else {
            return response("Unauthorized",401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return $this->ok();
    }

    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        $data->merge(['password_real' => Hash::make($data->password)]);
        User::create($data->all());
        Cache::forever('user', User::with('class_user')->get());
        return $this->ok();
    }

    public function get_all()
    {
        $data = Cache::rememberForever('user', function(){
            return User::with('class_user')->get();
        });
        return response((array)json_decode($data->toJson()),200);
    }

    public function get_current()
    {
        $data = User::with('class_user')->find(Auth::id());
        $data = json_decode($data->toJson());
        return response((array)$data,200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->edit_rules);
        if ($validator->fails()) return $this->fail($validator);
        if (Auth::user()->type != 0 && Auth::id() != (int)$id) return response("Unauthorized",400);
        if (isset($data->password) && $data->password != ""){
            $data->merge(['password' => Hash::make($data->password)]);
        }
        else $data->password = User::where('id', $id)->select('password_real')->first()->password_real;
        User::where('id', $id)
            ->update([
                "cn_name" => $data->cn_name,
                "en_name" => $data->en_name,
                "password_real" => $data->password,
                "email" => $data->email,
                "type" => $data->type,
            ]);
        Cache::forever('user', User::with('class_user')->get());
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        User::where('id', $id)->delete();
        Cache::forever('user', User::with('class_user')->get());
        return $this->ok();
    }
    
    public function forget_password(Request $data)
    {
        $validator = Validator::make($data->all(), $this->forget_rules);
        if ($validator->fails()) return $this->fail($validator);
        $user = User::where('username',$data->username)->get()->first();
        $error = "The username does not exist or the email is wrong.";
        if ($user == null) return response($error,400);
        if ($user->email != $data->email) return response($error,400);
        $password = Str::random(8);
        Mail::to($user->email)->send(new ResetPassword($user->en_name,$password));
        $user->password_real = Hash::make($password);
        $user->save();
        return response("Password changed succesfully! Please check your email.",200);
    }

    public function reset_password(Request $data)
    {
        $validator = Validator::make($data->all(), $this->reset_rules);
        if ($validator->fails()) return $this->fail($validator);
        $user = User::where('username',$data->username)->get()->first();
        if ($user == null) return response("User does not exist",400);
        if (!Hash::check($data->old_password, $user->password_real)) return response("Old password is wrong",400);
        $user->password_real = Hash::make($data->new_password);
        $user->save();
        return response("Password changed succesfully!",200);
    }

    public function reset_password_all()
    {
        $users = User::all();
        foreach ($users as $user){
            $password = Str::random(8);
            if ($user->password_real != "") continue;
            if ($user->email == "") continue;
            Mail::to($user->email)->send(new ResetPassword($user->en_name,$password));
            $user->password_real = Hash::make($password);
            $user->save();
        }
        return $this->ok();
    }

}