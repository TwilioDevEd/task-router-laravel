<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

/**
 * Class TaskRouterController
 * @package App\Http\Controllers
 */
class TaskRouterController extends Controller
{

    public function incomingCall()
    {
        return "Icoming call";
    }

    public function enqueueCall()
    {
        return "Enqueue call";
    }

    public function assignment()
    {
        return "Assignment";
    }

    public function events()
    {
        return "Events";
    }
}
