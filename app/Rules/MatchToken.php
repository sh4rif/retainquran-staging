<?php

namespace App\Rules;
  
use Illuminate\Contracts\Validation\Rule;
use DB;
  
class MatchToken implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(session('email')){
             $email  = session('email');
             $token = DB::table('password_resets')->where('email', $email)->orderBy('created_at', 'desc')->first(['token'])->token;
             if($value == $token)
             {
                return true;
             }
        }
        else{
            return "Token is required.";
        }
       
    }
   
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not correct.';
    }
}

?>
