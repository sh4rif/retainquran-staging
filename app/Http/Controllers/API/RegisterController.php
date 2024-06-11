<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\User_setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    // use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

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
    protected function validator(array $data)
    {
        
        return Validator::make($data, [
            'usr_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'usr_name' => $data['usr_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_login' => 0
        ]);
    }
    protected function usr_settings(array $data)
    {
        return User_setting::create($data);
    }

    public function createUser(Request $request)
    {
        $data = $request->only('usr_name', 'password', 'password_confirmation', 'email');

        $response = $this->validator($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ",$errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else {
            $result = 'success';   
            $user = $this->create($data);
            $usr_id = $user->id;
            //$login = User::where('id',$usr_id)->update(['is_login'  => 1]);
            $settings = [
                'usr_id'=> $usr_id,
                'reciter_id' => 18,
                'trans_id' => 1,
                'rtype_id' => 1,
                'translation_id' => 1,
                'language_id' => 1,
                'is_notify' => 1,
                'notification_time' => '08:00:00'
                    
            ];
            $usettings = $this->usr_settings($settings);
            $res = ['result' => $result, 'response' => $user];
            return response()->json($res, 200);
        }

        // $user = $this->create($data);
    }

    
}
