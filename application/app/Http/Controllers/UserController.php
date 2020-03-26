<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;	
use App\User;
use Auth;
use DB;

class UserController extends Controller
{

    private $rules = [
        "username" => "required|unique:users",
        "cn_name"  => ["required","regex:/[\x{4e00}-\x{9fa5}]+/u"],
        "en_name"  => "required",
        "password" => "required",
        "type"     => "required|integer|between:0,1",
    ];

    private $login_rules = [
        "username" => "required",
        "password" => "required"
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
        $data->merge(['password' => Hash::make($data->password)]);
        User::create($data->all());
        return $this->ok();
    }

    public function get_all()
    {
        $data = User::all();
        return response($data->toJson(),200);
    }

    public function get_current()
    {
        $data = User::find(Auth::id());
        $data = json_decode($data->toJson());
        $data->class_subject = DB::table('class_user')->where('user_id',Auth::id())
                                                      ->select('class_id','subject_id')
                                                      ->get();
        return response((array)$data,200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail($validator);
        if (Auth::user()->type != 0 && Auth::id() != $id) return response("Unauthorized",400);
        $data->merge(['password' => Hash::make($data->ic)]); 
        User::where('id', $id)
            ->update([
                "username" => $data->username,
                "cn_name" => $data->cn_name,
                "password" => $data->password,
                "type" => $data->type,
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        User::where('id', $id)->delete();
        return $this->ok();
    }

}