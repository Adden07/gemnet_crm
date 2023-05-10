<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Customize;
use Auth;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // public function __construct(){
    //     dd(Auth::user()->id);
    //     $this->customization();
    // }

    // //return customization from cache
    // private function customization(){
    //     Cache::rememberForever('customization',function(){
    //         // if(auth()->check()){
    //             $custom = Customize::where('admin_id',1)->first();
    //             if(!empty($custom)){
    //                 return json_decode($custom->data,true);
    //             }
    //             return NULL;
    //         //}
    //     });
    // }
}
