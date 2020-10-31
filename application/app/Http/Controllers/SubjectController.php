<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $this->clear_cache();
        return $this->ok();
    }

    public function get()
    {
        $data = Cache::rememberForever('subject', function(){
            return Subject::all();
        });
        return response((array)json_decode($data->toJson()),200);
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
        $this->clear_cache();
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        Subject::where('id', $id)->delete();
        $this->clear_cache();
        return $this->ok();
    }

}