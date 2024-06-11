<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Rules\MatchToken;
use App\Models\User;
use DB;

class ResetPasswordController extends Controller
{
    protected function sendResetResponse(Request $request)
    {
        //password.reset
        $input = $request->only('email', 'token', 'password', 'password_confirmation');
        $email = $request->input('email');
        session(['email' => $email]);
        $user = User::where('email', $email)->get()->first();
        $validator = Validator::make($input, [
            'token' => ['required', new MatchToken()],
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        if ($validator->fails()) {
            $response = array();
            $errors = $validator->errors()->all();
            $err_str = implode(" ",$errors);
            $response['response'] = $err_str;
            $response['result'] = 'failed';
            return response()->json($response);
        }
        else
        {
           $response = $user->forceFill([
                'password' => Hash::make($request->input('password'))
            ])->save();

           if($response)
           {
            DB::table('password_resets')
                    ->where('token', '=', $request->input('token'))
                    ->where('email', '=', $email)->delete();
            $message = "Password reset successfully.";
            $result = "success";
           }
           else{
            $message = "Token has expired.";
            $result = "failed";
            }
            $response = ['result' => $result, 'response' => $message];
            return response()->json($response);

        }
        
    }


}
