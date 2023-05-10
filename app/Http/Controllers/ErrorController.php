<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErrorController extends Controller
{
    public function index(Request $request){
        $method = 'error_'.$request->method;
        if(method_exists($this, $method)){
            return call_user_func(array($this, $method));
        }
        abort(404);
    }

    private function error_400(){
        abort(400);
    }

    private function error_401(){
        abort(401);
    }

    private function error_403(){
        abort(403, 'Access Denied');
    }

    private function error_404(){
        abort(404);
    }

    private function error_500(){
        abort(500);
    }
}