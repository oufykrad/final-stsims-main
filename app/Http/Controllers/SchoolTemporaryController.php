<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchoolTemporaryController extends Controller
{
    public function index(){
        return inertia('Modules/School/Temp/Index');
    }
}
