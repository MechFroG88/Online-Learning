<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Period;
use Validator;

class PeriodController extends Controller
{

    private $rules = [
        "start_time"  => "required|date",
        "end_time"    => "required|date",
    ];


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Period::create($data->all());
        return $this->ok();
    }

    public function get()
    {
        $data = Period::all();
        return response($data->toJson(),200);
    }

    public function delete(Request $data,$id)
    {
        Period::where('id', $id)->delete();
        return $this->ok();
    }

}