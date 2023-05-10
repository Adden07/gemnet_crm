<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetails;
use App\Services\Slug;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    // private $provider_users = ['independent_driver', 'broker', 'freight_forwarder'];
    // private $provider_users = ['carrier', 'independent_driver', 'broker', 'freight_forwarder'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function register(Request $request)
    {
        $rules = array(
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'mobile_no' => ['required', 'unique:user_details'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            // 'password' => ['required', 'string', 'min:6', 'confirmed'],
        );

        $err_msgs = array(
            'mobile_no.unique' => 'The Phone Number is already used by another Provider.',
            'email.unique' => 'The Email is already used by another Provider.'
        );

        if(in_array($request->user_type, ['carrier', 'broker'])){
            $rules['dot_no'] = ['required', 'string', 'unique:user_details'];
            $err_msgs['dot_no.unique'] = 'The DOT Number is already used by another Provider.';
        }

        if ($request->user_type == 'independent_driver') {
            $rules['cdl'] = ['required', 'string', 'unique:user_details'];
            $err_msgs['cdl.unique'] = 'The CDL is already used by another Provider.';
        }

        if ($request->user_type == 'broker') {
            $rules['mc_no'] = ['required', 'string', 'unique:user_details'];
            $err_msgs['mc_no.unique'] = 'The MC Number is already used by another Provider.';
        }

        $validator = Validator::make($request->all(), $rules, $err_msgs);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors(),
            ]);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = DB::transaction(function() use ($data){
            $user = User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            if ($data['user_type'] == 'shipper_individual' || $data['user_type'] == 'shipper_business') {
                $user->approved_at = now();
                $user->all_ratings = array(
                    'communication' => 5,
                    'payment' => 5,
                    'total_feedbacks' => 0
                );
            }else{
                $user->all_ratings = array(
                    'communication' => 5,
                    'care_of_goods' => 5,
                    'punctuality' => 5,
                    'service_as_quoted' => 5,
                    'total_feedbacks' => 0,
                    'on_time_percent' => 100,
                    'damage' => 100,
                );
            }
            $slug = new Slug();

            $company_name = $data['company_name'] ?? null;
            if($company_name == null){
                $company_name = $data['firstname'] . ' ' . $data['lastname'];
            }
            $_new_slug = strtotime($user->created_at).' '.$company_name;
            $user->slug = $slug->createSlug('users', $_new_slug);
            $user->overal_rating = 5;
            $user->email_verified_at = now();
            $user->claim_email = $data['email'];
            $user->user_type = $data['user_type'];
            $user->user_roles = '[]';
            $user->dba = $data['dba'] ?? null;
            $user->company_name = $company_name;
            if($data['campaign_id']){
                $user->registered_via = $data['campaign_id'];
            }

            if(\Str::contains(strtolower($user->email), ['@yopmail.com', '%@yopmain.com', '@mailinator.com'])){
                $user->is_dummy = 1;
            }

            $user->save();

            $this->save_user_details($data, $user->id);

            return $user;
        });

        if($user == null){
            return response()->json([
                'error' => 'User can not be registered at this moment please contact us for any furthur assistance.',
            ]);
        }

        return $user;
    }

    private function save_user_details($data, $user_id){
        $user_details = array(
            'user_id' => $user_id,
            'mobile_no' => $data['mobile_no'] ?? $data['phone'],
            'address' => $data['address'],
            'address_2' => $data['address_2'] ?? null,
            'state' => ($data['country'] == 'United States of America') ? $data['state'] : $data['state_field'],
            'city' => $data['city'],
            'zipcode' => $data['zipcode'],
            'country' => $data['country'],
        );

        if (in_array($data['user_type'], ['carrier', 'broker'])) {
            $user_details['dot_no'] = $data['dot_no'];
        }

        if ($data['user_type'] == 'independent_driver') {
            $user_details['cdl'] = $data['cdl'];
        }

        if ($data['user_type'] == 'broker') {
            $user_details['mc_no'] = $data['mc_no'];
        }

        $user_details = UserDetails::create($user_details);
    }

    protected function registered(Request $request, $user)
    {
        return response()->json([
            'success' => 'Your account has been successfully created.',// but you still need to verify you email',
            'redirect' => (session()->get('last_url')) ??  route('front.home')
        ]);
    }
}
