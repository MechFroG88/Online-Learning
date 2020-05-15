<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\_Class;
use Validator;
use DB;
use Auth;

class _ClassController extends Controller
{

    private $rules = [
        "cn_name"  => "required",
        "en_name"  => "required",
        "user_id"  => "required|integer",
        "code"     => "required",
    ];


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        _Class::create($data->all());
        Cache::forever('class', _Class::all());
        return $this->ok();
    }

    public function get()
    {
        if (Auth::user()->type != 0){
            $data = Cache::rememberForever('class', function(){
                return _Class::all();
            });
            return response((array)json_decode($data->toJson()),200);
        } else {
            $data = DB::table('classes')->where('deleted_at',null)->get();
            return response((array)json_decode($data->toJson()),200);
        }
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail($validator);
        if (User::find($data->user_id)->get() == null) return $this->fail("The User does not exist");
        _Class::where('id', $id)
            ->update([
                "cn_name" => $data->cn_name,
                "en_name" => $data->en_name,
                "user_id" => $data->user_id,
                "code"    => $data->code,
            ]);
        Cache::forever('class', _Class::all());
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        _Class::where('id', $id)->delete();
        Cache::forever('class', _Class::all());
        return $this->ok();
    }

}