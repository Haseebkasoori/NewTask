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

                    $data['error']="You didn't confirm your email yet!!";
                    $data['message']="Someting went Worng";
                    return response()->error($data,400);
                }else{

                    request()->merge(['user_data'=>$user_data]);
                    return $next($request);
                }
            }else{
                $data['error']="Email not Register";
                $data['message']="Someting went Worng";
                return response()->error($data,400);
            }
        }catch(\Exception $ex){
            $data['error']=$ex->getMessage();
            $data['message']="Someting went Worng";
            return response()->error($data,500);
        }
    }
}
