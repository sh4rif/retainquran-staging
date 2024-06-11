<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    protected function sendResetLinkResponse_new(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $err_str = implode(" ", $errors);

            return response(['result' => 'failed', 'response' => $err_str], 422);
        }
        $email_verify = '';
        $provider = '';
        $usr_name = '';
        $emaill = User::where('email', $input)->first(['email','provider','usr_name']);
       
        if ($emaill) {
          $usr_name = $emaill->usr_name;
          $email_verify = $emaill->email;
          $provider = $emaill->provider;
        }
        
        session(['usr_name' => $usr_name]);

        if (!empty($email_verify)) {
            if($provider != null && ($provider == 'google' || $provider == 'facebook')){
                $message = "This Email is Registered from Google/Facebook";
                $result = 'failed';

            }else
            {
                $response =  Password::sendResetLink($input);
                if ($response == Password::RESET_LINK_SENT) {
                    $message = "Mail send successfully";
                    $result = 'success';
                } else {
                    $message = "Please retry after 2 Minutes";
                    $result = 'failed';
                }
                
            }

            
        } else {
            $message = "This Email is not Registered";
            $result = 'failed';
        }
        
        $response = ['result' => $result, 'response' => $message];
        return response($response, 200);
    }


    // have issue in the token
    protected function sendResetLinkResponse(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $err_str = implode(" ", $errors);

            return response(['result' => 'failed', 'response' => $err_str], 422);
        }

        $email_verify = '';
        $provider = '';
        $usr_name = '';
        $emaill = User::where('email', $input)->first(['email','provider','usr_name']);

        if ($emaill) {
          $usr_name = $emaill->usr_name;
          $email_verify = $emaill->email;
          $provider = $emaill->provider;
        }

        session(['usr_name' => $usr_name]);


        if (!empty($email_verify)) {
            if($provider != null && ($provider == 'google' || $provider == 'facebook')){
                $message = "This Email is Registered from Google/Facebook";
                $result = 'failed';

            }else
            {
                $token = random_int(100000, 999999);

                DB::table('password_resets')
                    ->updateOrInsert(
                        ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()],
                        ['email' => $request->email]
                    );

                Mail::send('emails.resetpasswordtoken', ['token' => $token], function($message) use($request){
                    $message->to($request->email);
                    $message->subject('Reset Password Notification');
                });

                $message = "Mail send successfully";
                $result = 'success';

                
            }


        } else {
            $message = "This Email is not Registered";
            $result = 'failed';
        }

        $response = ['result' => $result, 'response' => $message];
        return response($response, 200);
    }
}
