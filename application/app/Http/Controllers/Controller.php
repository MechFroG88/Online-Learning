<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function fail($validator)
    {
        $error = "";
        foreach ($validator->messages()->getMessages() as $field_name => $messages)
        {
            $error += $messages + "<br>";
        }
        return response("Validation error : <br>" + $error,400);
    }

    protected function ok()
    {
        return response("Successful operation",200);
    }
}
