<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\JwtAuthentication;

class JwtMiddleware
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
        try{
            try{
                $decoded=JwtAuthentication::varifyToken(request()->bearerToken());
            }catch(\Exception $ex){
                $data['message']=$ex->getMessage();
                return response()->error($data,404);
            }
            $user_data=User::where('email',$decoded->data->email)->first();
            // check if user data exist
            if (empty($user_data['jwt_token'])) {

                throw new \Exception('LogOut, Please Login');

            }else{
                request()->merge(['user_data'=>$user_data,'decoded_data'=>$decoded]);
                return $next($request);
            }
        }catch(\Exception $ex){
            $data['message']=$ex->getMessage();
            return response()->error($data,404);
        }
    }
}
