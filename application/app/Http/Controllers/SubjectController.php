<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subject;
use Validator;

class SubjectController extends Controller
{

    private $rules = [
        "cn_name"  => "required",
        "en_name" => "required",
        "day"  => "required|integer",
        "week" => "required|integer"
    ];


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Subject::create($data->all());
        return $this->ok();
    }

    public function get()
    {
        $data = Subject::all();
        return response($data->toJson(),200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Subject::where('id', $id)
            ->update([
                "cn_name" => $data->cn_name,
                "day" => $data->day,
                "week" => $data->week,
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        Subject::where('id', $id)->delete();
        return $this->ok();
    }

}