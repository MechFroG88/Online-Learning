<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Choice;
use Validator;
use App\Event;
use App\User;
use App\Class_user;
use App\Subject;

class ChoiceController extends Controller
{

    private $edit_rules = [
        "method"         => "",
        "link"           => "",
        "streamid"       => "",
        "streamPassword" => "",
        "description"    => "",
    ];

    private $rules = [
        "event_id"   => "required|integer",
        "user_id"    => "required|integer",
        "subject_id" => "required|integer",
        "period_id"  => "required|integer",
        "date"       => "required|date",
        "class_id"   => "required|integer",
    ];

    public function submit(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail($validator);
        $error = check($data->all());
        if (isset($error)) return response($error,400);
        Choice::create($data->all());
        return $this->ok();
    }

    public function get_all()
    {
        $choices = Choice::all();
        $data = [];
        foreach ($choices as $choice){
            $choice = json_decode($choice->toJson());
            $choice->cn_name = $choice->class_user->user->cn_name;
            $choice->class_id = $choice->class_user->class_id;
            $choice->subject_id = $choice->class_user->subject_id;
            unset($choice->class_user_id);
            unset($choice->class_user);
            array_push($data,$choice);
        }
        return response($data,200);
    }

    public function get_related()
    {
        $user_class_ids = DB::table('class_user')->where('user_id',Auth::id())->select('id')->get();
        $ids = [];
        foreach($id as $user_class_ids){
            array_push($ids,$id->id);
        }

        $choices = Choice::whereIn('id',$ids)->get();
        $data = [];
        foreach ($choices as $choice){
            $choice = json_decode($choice->toJson());
            $choice->cn_name = $choice->user_class->user->cn_name;
            $choice->class_id = $choice->user_class->class_id;
            $choice->subject_id = $choice->user_class->subject_id;
            unset($choice->user);
            array_push($data,$choice);
        }
        return response($data,200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->edit_rules);
        if ($validator->fails()) return $this->fail($validator);
        Choice::where('id', $id)
            ->update([
                "method"         => isset($data->method) ? $data->method : "",
                "link"           => isset($data->link) ? $data->link : "",
                "streamid"       => isset($data->streamid) ? $data->streamid : "",
                "streamPassword" => isset($data->streamPassword) ? $data->streamPassword : "",
                "description"    => isset($data->description) ? $data->description : "",
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        Choice::where('id', $id)->delete();
        return $this->ok();
    }

    private function check($data)
    {
        $event = Event::find($data->event_id);
        $subject = Subject::find($data->subject_id);
        $user = DB::table('users')->find($data->user_id)->doesntExists();
        $period = DB::table('periods')->find($data->period_id)->doesntExists();
        $class = DB::table('classes')->find($data->class_id)->doesntExists();
        $date = strtotime($data->$date);
        $now = date('Y-m-d H:i:s');

        if ($event == null) return "Event does not exist";
        if ($subject == null) return "Subject does not exist";
        if ($user) return "Teacher does not exist";
        if ($period) return "Period does not exist";
        if ($class) return "Class does not exist";
        if ($date > $event->end_date || $date < $event->start_date) return "Date not in range";
        
        if (Auth::user()->type != 0){
            if (Auth::id() != $data->user_id) return "Unauthorized";
            if ($now > $event->end_pick_date || $now < $event->start_pick_date) return "Date not in range";
        }

        $id = DB::table('class_user')->where([
            "user_id" => $data->user_id,
            "class_id" => $data->class_id,
            "subject_id" => $data->subject_id,
        ])->select('id')->first();

        if (!isset($id)) return "Teacher does not teach this class or subject";

        $user_class_ids = DB::table('class_user')->where('class_id',$data->class_id)->select('id')->get();
        $ids = [];
        foreach($user_class_ids as $id){
            array_push($ids,$id->id);
        }

        if (DB::table('choices')->whereIn('id',ids)
        ->where([
            "event_id" => $data->event_id,
            "period_id" => $data->period_id
        ])->exists()) return "The current period has been chosen";
        
        if(DB::table('choices')->whereDate('date',$now)
        ->where([
            "user_class_id" => $id,
            "event_id" => $data->event_id,
        ])->count() >= $subject->day) return "The daily limit of the subject is reached";
        
        if(DB::table('choices')->whereBetween('date',[strtotime("monday this week"),strtotime("sunday this week")])
        ->where([
            "user_class_id" => $id,
            "event_id" => $data->event_id,
        ])->count() >= $subject->week) return "The weekly limit of the subject is reached";

    }

}