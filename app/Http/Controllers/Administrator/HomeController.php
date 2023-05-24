<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Administrator\AdminController;
use Illuminate\Support\Facades\DB;

class HomeController extends AdminController
{
    public function index()
    {   
        $time = now()->addMinutes(20);
        // dd(auth()->user()->user_permissions);
        $data = array(
            "title" => "Dashboad",
        );

        // if(auth('admin')->check() && auth('admin')->user()->can('users-read')){
        //         $users_count = cache()->remember('admin_users_count', $time, function (){
        //         return DB::select('SELECT COUNT(`id`) AS `total`, `user_type` FROM `users` WHERE `is_dummy` = 0 GROUP BY `user_type`');
        //     });
        //     $total_shippers = $total_providers = 0;
    
        //     foreach($users_count as $user){
        //         if($user->user_type == 'shipper_business' || $user->user_type == 'shipper_individual'){
        //             $total_shippers += $user->total;
        //         }else{
        //             $total_providers += $user->total;
        //         }
        //     }
    
        //     $unapproved_providers = cache()->remember('admin_unapproved_providers', $time, function (){
        //         $total = DB::select('SELECT COUNT(`id`) AS `total` FROM `users` WHERE `approved_at` IS NULL AND `is_dummy` = 0');
        //         return $total[0]->total ?? 0;
        //     });
    
        //     $ltl_carriers = cache()->remember('admin_ltl_carriers', $time, function (){
        //         $total = DB::select('SELECT COUNT(`id`) AS `total` FROM `users` WHERE `is_instant_carrier` = 1 AND `is_dummy` = 0');
        //         return $total[0]->total ?? 0;
        //     });

        //     $data["total_shippers"] = $total_shippers;
        //     $data["total_providers"] = $total_providers;
        //     $data["unapproved_providers"] = $unapproved_providers;
        //     $data["ltl_carriers"] = $ltl_carriers;
        // }

        // if(auth('admin')->check() && auth('admin')->user()->can('shipment-read')){
        //     $shipments_count = cache()->remember('admin_shipments_count', $time, function (){
        //         return DB::select('SELECT COUNT(`id`) AS `total`, `status` FROM `shipments` WHERE `is_dummy` = 0 GROUP BY `status`');
        //     });
    
        //     $total_shipments = $booked_shipments = $delivered_shipments = $cancelled_shipments = 0;
        //     $booked_shipments_filter = ['cancelled', 'pending', 'accepted', 'expired', 'payment_failed'];
        //     foreach($shipments_count as $shipment){
        //         $total_shipments += $shipment->total;
        //         if(!in_array($shipment->status, $booked_shipments_filter)){
        //             $booked_shipments += $shipment->total;
        //         }
        //         if($shipment->status == 'delivered'){
        //             $delivered_shipments += $shipment->total;
        //         }
    
        //         if($shipment->status == 'cancelled'){
        //             $delivered_shipments += $shipment->total;
        //         }
        //     }

        //     $data["booked_shipments"] = $booked_shipments;
        //     $data["delivered_shipments"] = $delivered_shipments;
        //     $data["cancelled_shipments"] = $cancelled_shipments;
        //     $data["total_shipments"] = $total_shipments;
        // }

        // if(auth('admin')->check() && auth('admin')->user()->can('payments-read')){
        //     $total_payments = cache()->remember('admin_total_payments', $time, function (){
        //         $status = "'delivered','booked','in_transit','picked_up'";
        //         $total_payments = DB::select("SELECT SUM(total_amount) AS `total_amount`, SUM(booking_fee) AS `booking_fee`, SUM(`freight_charge`) AS `freight_charge`, SUM(`insurance`) AS `insurance`, SUM(`premium_service`) AS `premium_service` FROM `shipments` WHERE `status` IN ($status) AND `is_dummy` = 0")[0];
        //         return $total_payments;
        //     });

        //     $data["total_payments"] = $total_payments->total_amount ?? 0;
        //     $data["booking_fee"] = $total_payments->booking_fee ?? 0;
        //     $data["insurance_charges"] = $total_payments->insurance ?? 0;
        //     $data["premium_services"] = $total_payments->premium_service ?? 0;
        //     $data["freight_charges"] = $total_payments->freight_charge ?? 0;
            
        // }

        // if(auth('admin')->check() && auth('admin')->user()->can('payouts-read')){
        //     $total_payouts = cache()->remember('admin_total_payouts', $time, function (){
        //         $total = DB::select("SELECT SUM(amount) AS total FROM payouts WHERE STATUS = 'approved'");
        //         return $total[0]->total ?? 0;
        //     });

        //     $data["total_payouts"] = $total_payouts;
        // }

        return view('admin.home')->with($data);
    }
}
