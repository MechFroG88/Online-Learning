<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Exports\ChoiceExport;
use Maatwebsite\Excel\Facades\Excel;
  
class ExportController extends Controller
{

    public function export() 
    {
        return Excel::download(new ChoiceExport, 'choices.xlsx');
    }
}