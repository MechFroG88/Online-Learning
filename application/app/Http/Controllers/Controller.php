<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function fail($validator)
    {
        $error = "";
        $messages = $validator->messages();
        foreach ($messages->all('<li>:message</li>') as $message)
        {
            $error = $error . $message;
        }
        return response("Validation error : <br>" . $error,400);
    }

    protected function ok()
    {
        return response("Successful operation",200);
    }

    public function clear_cache()
    {
        Cache::flush();
    }
}
