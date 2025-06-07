<?php

namespace App\Http\Controllers\Rescuer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RescuerController extends Controller
{
    public function index(){
        return view('dashboard');
    }
}

// ^Multiauth