<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Choice;
use Validator;
use App\Event;
use App\User;
use App\Period;
use App\Class_user;
use App\_Class;
use App\Subject;
use DB;
use DateTime;
use Auth;

class ChoiceController extends Controller
{
    private $class_user_id;

    private $edit_rules = [
        "method"         => "",
        "link"           => "",
        "streamId"       => "",
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
        $error = $this->check_condition($data->all());
        if (isset($error)) return response($error,400);
        $data = $data->all();
        foreach($this->edit_rules as $key => $value){
            $data[$key] = $value; 
        }
        $data['class_user_id'] = $this->class_user_id;
        $choice = Choice::create($data);
        return $this->get($choice->id);
    }

    public function get($choice_id)
    {
        $choice = Choice::find($choice_id);
        $choice = json_decode($choice->toJson());
        $choice->cn_name = $choice->class_user->user->cn_name;
        $choice->en_name = $choice->class_user->user->en_name;
        $choice->class_id = $choice->class_user->class_id;
        $choice->user_id = $choice->class_user->user_id;
        $choice->subject_id = $choice->class_user->subject_id;
        unset($choice->class_user_id);
        unset($choice->class_user);
        return response((array)$choice,200);
    }

    public function get_all()
    {
        $choices = Choice::all();
        $data = [];
        foreach ($choices as $choice){
            $choice = json_decode($choice->toJson());
            $choice->cn_name = $choice->class_user->user->cn_name;
            $choice->en_name = $choice->class_user->user->en_name;
            $choice->class_id = $choice->class_user->class_id;
            $choice->user_id = $choice->class_user->user_id;
            $choice->subject_id = $choice->class_user->subject_id;
            unset($choice->class_user_id);
            unset($choice->class_user);
            array_push($data,$choice);
        }
        return response($data,200);
    }

    public function get_related($id = null)
    {
        if ($id == null) $id = Auth::id();
        $class_user_ids = Class_user::where('user_id',$id)->select('class_id')->get();
        $class_ids = [];
        foreach($class_user_ids as $id){
            array_push($class_ids,$id->class_id);
        }

        $ids = [];
        $class_user_ids = Class_user::whereIn('class_id',$class_ids)->select('id')->get();
        foreach($class_user_ids as $id){
            array_push($ids,$id->id);
        }

        $active_events = [];
        $events = Event::all();
        foreach ($events as $event){
            array_push($active_events,$event->id);
        }

        $choices = Choice::whereIn('class_user_id',$ids)
                         ->whereIn('event_id',$active_events)->get();
        $data = [];
        foreach ($choices as $choice){
            $choice = json_decode($choice->toJson());
            $choice->cn_name = $choice->class_user->user->cn_name;
            $choice->en_name = $choice->class_user->user->en_name;
            $choice->class_id = $choice->class_user->class_id;
            $choice->user_id = $choice->class_user->user_id;
            $choice->subject_id = $choice->class_user->subject_id;
            unset($choice->class_user_id);
            unset($choice->class_user);
            if (Auth::user()->type != 0 && $choice->user_id != Auth::id()){
                unset($choice->method);
                unset($choice->link);
                unset($choice->streamId);
                unset($choice->streamPassword);
                unset($choice->description);
            }
            array_push($data,$choice);
        }
        return response($data,200);
    }

    public function get_related_id($id)
    {
        return $this->get_related($id);
    }

    public function edit(Request $data,$id)
    {
        $choice = Choice::find($id);
        $validator = Validator::make($data->all(),$this->edit_rules);
        if ($choice->class_user->user_id != Auth::id() && Auth::user()->type != 0) return response("Unauthorized",400);
        if ($validator->fails()) return $this->fail($validator);
        Choice::where('id', $id)
            ->update([
                "method"         => isset($data->method) ? $data->method : "",
                "link"           => isset($data->link) ? $data->link : "",
                "streamId"       => isset($data->streamId) ? $data->streamId : "",
                "streamPassword" => isset($data->streamPassword) ? $data->streamPassword : "",
                "description"    => isset($data->description) ? $data->description : "",
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        if (Auth::user()->type != 0){
            $choice = Choice::with('event')->where('id',$id)->first();
            $event = $choice->event;
            $now = date('Y-m-d H:i:s'); 
            if (Auth::id() != $choice->class_user->user_id) return response("Unauthorized",400);
            if ($now > $event->end_pick_datetime) return response("The selection period has over",400);
            if ($now < $event->start_pick_datetime) return response("The selection period has not started",400);
        }
        Choice::where('id', $id)->delete();
        return $this->ok();
    }

    private function check_condition($data)
    {
        $event = Event::find($data['event_id']);
        $subject = Subject::find($data['subject_id']);
        $user = User::find($data['user_id']);
        $period = Period::find($data['period_id']);
        $class = _Class::find($data['class_id']);
        $date = DateTime::createFromFormat('Y-m-d H:i:s',$data['date']." 00:00:00");
        $now = date('Y-m-d H:i:s');

        if ($event == null) return "Event does not exist";
        if ($subject == null) return "Subject does not exist";
        if ($user == null) return "Teacher does not exist";
        if ($period == null) return "Period does not exist";
        if ($class == null) return "Class does not exist";
        if ($date > $event->end_date || $date < $event->start_date) return "Date not in range";
        
        if (Auth::user()->type != 0){
            if (Auth::id() != $data['user_id']) return "Unauthorized";
            if ($now > $event->end_pick_datetime) return "The selection period has over";
            if ($now < $event->start_pick_datetime) return "The selection period has not started";
        }

        $this->class_user_id = Class_user::where([
            "user_id" => $data['user_id'],
            "class_id" => $data['class_id'],
            "subject_id" => $data['subject_id'],
        ])->select('id')->first();

        if (!isset($this->class_user_id)) return "Teacher does not teach this class or subject";
        $this->class_user_id = $this->class_user_id->id;

        $class_user_ids = Class_user::where('class_id',$data['class_id'])->select('id')->get();
        $ids = [];
        foreach($class_user_ids as $id_){
            array_push($ids,$id_->id);
        }
        $choices = DB::table('choices')->where([
                                           "event_id" => $data['event_id'],
                                       ])->get();
        sort($ids);
        $daily = 0;
        $weekly = 0;
        $exist = false;
        foreach ($choices as $choice){
            if ($choice->class_user_id == $this->class_user_id){
                $weekly++;
                if (date_create($choice->date)->format('Y-m-d') == $date->format('Y-m-d')){
                    $daily++;
                }
            }
            if (date_create($choice->date)->format('Y-m-d') == $date->format('Y-m-d') &&
                $choice->period_id == $data['period_id'] &&
                in_array($choice->class_user_id,$ids)){
                    $exist = true;
                    break;
                }           
        }
        if ($exist) return "The current period has been chosen";

        if ($daily >= $subject->day) return "The daily limit of the subject is reached";

        if ($weekly >= $subject->week) return "The weekly limit of the subject is reached";
    }
}