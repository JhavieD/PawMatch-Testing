<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RescuerController extends Controller
{
    public function index()
    {
        return view('rescuer.rescuer_dashboard');
    }
}
