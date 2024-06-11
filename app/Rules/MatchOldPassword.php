<?php

namespace App\Rules;
  
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

  
class MatchOldPassword implements Rule
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
             $password = User::where('email', $email)->first(['password'])->password;
             return Hash::check($value, $password);
        }
        else{
            return "Email field is required.";
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
