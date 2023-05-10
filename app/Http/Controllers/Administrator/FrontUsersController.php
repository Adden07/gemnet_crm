<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Models\User;
use Illuminate\Http\Request;

class FrontUsersController extends AdminController
{
    public function index(Request $request)
    {
        abort_if(!auth('admin')->user()->can('users-read'), 401);

        $users = $users = User::withCount(['quotes', 'shipments'])->with('user_details:id,user_id,image,mobile_no');
        if(isset($request->from) && isset($request->to)){
            $from = $request->from ?? date('Y-m-1');
            $to = $request->to ?? now();
            $users->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        if(isset($request->user_type)){
            if($request->user_type == 'shipper'){
                $users->shippers();
            }
            if ($request->user_type == 'provider') {
                $users->providers();
            }
        }
        if (isset($request->s)) {
            $users->whereLike(['firstname', 'lastname', 'email', 'company_name', 'dba'], $request->s);
        }
        
        if(!isset($request->show_dummy)){
            $users->whereIsDummy(0);
        }
        
        $data = array(
            'title' => 'All Users',
            'users' => $users->latest()->paginate(config('app.admin_pagination'))
        );
        return view('admin.users.all_users')->with($data);
    }

    public function view_user_profile($id)
    {
        abort_if(!auth('admin')->user()->can('users-read'), 401);

        $user = User::with('user_details')->withCount(['quotes', 'shipments'])->has('user_details')->whereSlug($id)->firstOrFail();
        $data = array(
            'is_edit' => true,
            'user' => $user,
        );

        $data['title'] = $data['user']->full_name . " Profile";
        $data['user_details'] = $data['user']->user_details;

        if($user->is_provider){
            $total_balance = CommonHelpers::get_provider_balance($user->id);

            $shipments_hold_amount = \App\Models\Shipment::whereProviderId($user->id)->whereIn('status', ['booked', 'in_transit', 'picked_up', 'delivered'])->whereNull('shipper_delivered_at')->sum('provider_amount');
            
            $data['total_balance'] = $total_balance;
            $data['hold_amount'] = $shipments_hold_amount;
            $data['total_paid_amount'] = \App\Models\Payout::whereUserId($user->id)->where('status', 'approved')->sum('amount');
            $data['user_bank'] = $user->user_bank;
        }

        return view('admin.users.user_profile')->with($data);
    }

    public function change_status($id)
    {
        abort_if(!auth('admin')->user()->can('users-write'), 401);
        $user = User::hashidFind($id);
        if ($user) {
            if(!$user->is_active){
                $user->is_active = 1;
                $user->save();
                return response()->json([
                    'success' => 'User status has been changed successfully',
                    'reload' => true
                ]);
            }

            $user->is_active = 0;
            $user->save();

            if($user->is_provider){
                $user->active_marketplace_quotes()->delete();
            }
            $user->notify(new \App\Notifications\User\UserBlocked($user));
            return response()->json([
                'success' => 'User status has been changed successfully',
                'reload' => true
            ]);
        }
        return response()->json([
            'success' => 'Something Went Wrong',
        ]);
    }

    public function verify_user($id)
    {
        abort_if(!auth('admin')->user()->can('users-write'), 401);
        $user = User::hashidFind($id);
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
            return response()->json([
                'success' => 'User has been verified successfully',
                'reload' => true
            ]);
        }
        return response()->json([
            'success' => 'Something Went Wrong',
        ]);
    }

    public function approve_user($id)
    {
        abort_if(!auth('admin')->user()->can('users-write'), 401);
        $user = User::hashidFind($id);
        if ($user) {
            $user->is_active = 1;
            $user->approved_at = \Carbon\Carbon::now();
            $user->save();
            if($user->is_provider){
                $user->notify(new \App\Notifications\User\ProviderApproved($user));
            }
            return response()->json([
                'success' => 'User status has been approved successfully',
                'reload' => true
            ]);
        }
        return response()->json([
            'success' => 'Something Went Wrong',
        ]);
    }

    public function delete($id)
    {
        abort_if(!auth('admin')->user()->can('users-delete'), 401);
        $user = User::hashidFind($id)->delete();
        if ($user) {
            return response()->json([
                'success' => 'User Deleted Successfully',
                'reload' => true
            ]);
        }
        return response()->json([
            'error' => 'Something Went Wrong',
        ]);
    }

    function update_certification(Request $request){
        abort_if(!auth('admin')->user()->can('users-write'), 401);
        $user_details = \App\Models\UserDetails::hashidOrFail($request->user_details_id);
        $user_details->dot_no = $request->dot_no;
        $user_details->mc_no = $request->mc_no;
        $user_details->ff_no = $request->ff_no;
        $user_details->nvocc_no = $request->nvocc_no;
        $user_details->cdl = $request->cdl;
        $user_details->save();
        return response()->json([
            'success' => 'User Certifications updated successfully',
            'reload' => true
        ]);
    }

    function edit_profile(Request $request){
        abort_if(!auth('admin')->user()->can('users-right'), 401);
        $user = User::hashidOrFail($request->user_id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->claim_email = $request->claim_email;
        $user->dispatch_email = $request->dispatch_email;
        $user->dispatch_phone_no = $request->dispatch_phone_no;
        $user->carrier_email = $request->carrier_email;
        $user->company_name = $request->company_name;
        $user->dba = $request->dba;
        $user->save();

        $user_details = \App\Models\UserDetails::hashidOrFail($request->user_details_id);
        $user_details->mobile_no = $request->mobile_no;
        $user_details->country = $request->country;
        $user_details->city = $request->city;
        $user_details->address = $request->address;
        $user_details->address_2 = $request->address_2;
        $user_details->state = $request->state;
        $user_details->zipcode = $request->zipcode;
        $user_details->company_website = $request->company_website;
        $user_details->no_of_drivers = $request->no_of_drivers;
        $user_details->no_of_dispatch = $request->no_of_dispatch;
        $user_details->no_of_trucks = $request->no_of_trucks;
        $user_details->terms_conditions = $request->terms_conditions;
        $user_details->insurance_coverage = $request->insurance_coverage;
        $user_details->carrier_code = $request->carrier_code;
        $user_details->operations = $request->operations;
        if ($request->hasFile('image')) {
            $image = CommonHelpers::uploadSingleFile($request->file('image'), 'uploads/user_profile/');
            if (is_array($image)) {
                return response()->json($image);
            }
            @unlink($user_details->image);
            $user_details->image = $image;
            $user->image = $image;
            $user->save();
        }
        $user_details->save();
        return response()->json([
            'success' => 'User Profile updated successfully',
            'reload' => true
        ]);
    }
}
