<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class VerifiedEmail
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
        $request_data=request()->json()->all();

        try{
            $user_data=User::where('email',$request_data['email'])->first();
            if(!empty($user_data->email_verified_at)){
                if ($user_data->email_verified_at===null) {
                    throw new \Exception("You didn't confirm your email yet!!");
                }else{

                    request()->merge(['user_data'=>$user_data]);
                    return $next($request);
                }
            }else{
                throw new \Exception("Email not Register");
            }
        }catch(\Exception $ex){
            $data['message']=$ex->getMessage();
            return response()->error($data,500);
        }
    }
}
