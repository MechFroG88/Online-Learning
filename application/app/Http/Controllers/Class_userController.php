<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Class_user;
use Validator;

class Class_userController extends Controller
{

    private $rules = [
        "user_id" => "required|integer",
        "class_id" => "required|integer",
        "subject_id" => "required|integer",
    ];


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);

        if(Class_user::where([
            "user_id" => $data['user_id'],
            "class_id" => $data['class_id'],
            "subject_id" => $data['subject_id']
        ])->exists()) return response("The same record has been created",400);

        $deleted = Class_user::onlyTrashed()->where([
            "user_id" => $data['user_id'],
            "class_id" => $data['class_id'],
            "subject_id" => $data['subject_id']
        ])->first();

        if ($deleted == null){
            Class_user::create($data->all());
        } else {
            $deleted->restore();
        }
        Cache::forever('user', User::with('class_user')->get());
        return $this->ok();
    }

    public function get()
    {
        $data = Class_user::all();
        return response((array)json_decode($data->toJson()),200);
    }

    public function delete(Request $data,$id)
    {
        Class_user::where('id', $id)->delete();
        Cache::forever('user', User::with('class_user')->get());
        return $this->ok();
    }

}