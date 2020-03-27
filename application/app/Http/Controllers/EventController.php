<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Validator;
use DB;

class EventController extends Controller
{

    private $rules = [
        "start_date"      => "required|date",
        "end_date"        => "required|date",
        "start_pick_datetime" => "required|date",
        "end_pick_datetime"   => "required|date",
    ];

    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Event::create($data->all());
        return $this->ok();
    }

    public function get()
    {
        $data = Event::all();
        return response((array)json_decode($data->toJson()),200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Event::where('id', $id)
            ->update([
                "start_date" => $data->start_date,
                "end_date" => $data->end_date,
                "start_pick_datetime" => $data->start_pick_datetime,
                "end_pick_datetime" => $data->end_pick_datetime,
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        Event::where('id', $id)->delete();
        return $this->ok();
    }
}