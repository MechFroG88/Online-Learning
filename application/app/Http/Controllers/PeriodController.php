<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Period;
use Validator;

class PeriodController extends Controller
{

    private $rules = [
        "start_time"  => "required|date_format:H:i:s",
        "end_time"    => "required|date_format:H:i:s|after:start_time",
    ];


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        Period::create($data->all());
        $this->clear_cache();
        return $this->ok();
    }

    public function get()
    {
        $data = Cache::rememberForever('period', function(){
            return Period::all();
        });
        return response((array)json_decode($data->toJson()),200);
    }

    public function delete(Request $data,$id)
    {
        Period::where('id', $id)->delete();
        $this->clear_cache();
        return $this->ok();
    }

}