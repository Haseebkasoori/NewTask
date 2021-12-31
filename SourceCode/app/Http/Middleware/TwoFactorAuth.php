<?php

namespace App\Http\Middleware;

use App\Models\TwoFactorAuth as ModelsTwoFactorAuth;
use Closure;
use Illuminate\Http\Request;

class TwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        app('App\Http\Requests\TwoFactorAuthRequest');
        try{
            $two_fa=ModelsTwoFactorAuth::where('user_id',request()->decoded_data->data->id)->first();
            if(!empty($two_fa)){
                if($two_fa->otp_mail !=  request('otp_mail')){
                    throw new \Exception('Incorrect mail OTP');
                }elseif($two_fa->otp_sms !=  request('otp_sms')){
                    throw new \Exception('Incorrect SMS OTP');
                }elseif($two_fa->otp_authenticator !=  request('otp_authenticator')){
                    throw new \Exception('Incorrect Authenticator OTP');
                }else{
                    request()->merge(['two_fa'=>$two_fa]);
                    return $next($request);
                }
            }
        }catch(\Exception $ex){

            $data['message']=$ex->getMessage();
            return response()->error($data,404);
        }
    }
}
