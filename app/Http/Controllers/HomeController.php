<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class HomeController extends Controller
{
    public function index(){
        return inertia('Modules/Home/Index');
    }

    public function settings(Request $request){
        $bearer = $request->bearerToken();
        $token = PersonalAccessToken::findToken($bearer);
        $agency = $token->tokenable->profile->agency->id;
        return $agency;
    }
}
