<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Exports\ChoiceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Event;
  
class ExportController extends Controller
{

    public function export(Request $data,$id) 
    {
        $event = Event::withTrashed()->find($id);
        if ($event == null) return response("event id not found",400);
        $name = "教师课程选择" . date_format($event->start_date,"Y-m-d") . "至" . date_format($event->end_date,"Y-m-d");
        return Excel::download(new ChoiceExport($id), $name . '.xlsx');
    }
}