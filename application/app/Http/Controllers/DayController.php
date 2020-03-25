<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Day;
use Validator;

class DayController extends Controller
{

    private $rules = [
        "count"  => "required|integer",
    ];

    public function get()
    {
        $data = Day::all();
        return response($data->toJson(),200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Day::where('id', $id)
            ->update([
                "count" => $data->count,
            ]);
        return $this->ok();
    }

}