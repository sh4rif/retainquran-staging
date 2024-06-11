<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
use Illuminate\Auth\Events\PasswordReset;

class UpdatePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    protected function sendUpdateResponse(Request $request)
    {
        //password.update

        $input = $request->only('email', 'old_password', 'password', 'password_confirmation');

        $email = $request->input('email');
        $user = User::where('email', $email)->get()->first();

        session(['email' => $email]);
        $validator = Validator::make($input, [
            'old_password'  => ['required', new MatchOldPassword()],
            'email'         => 'required|email',
            'password'      => 'required|confirmed|min:8',
        ]);
        if ($validator->fails()) {
            $response = array();
            $errors = $validator->errors()->all();
            $err_str = implode(" ", $errors);
            $response['response'] = $err_str;
            $response['result'] = 'failed';
            return response()->json($response);
        }
        
        else{

            $user->password = bcrypt($request->get('password'));
            $user->save();
            $response['response'] = "Password Updated successfully";
            $response['result'] = 'success';

            return json_encode($response);
        }
    }
}
