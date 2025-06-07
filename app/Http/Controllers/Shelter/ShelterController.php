<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    public function index(){
        return view('shelter.dashboard');
    }
}

// ^Multiauth