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
                $data['error']=$ex->getMessage();
                $data['message']="Someting went Worng with Bearer Token";
                return response()->error($data,404);
            }
            $user_data=User::where('user_name',$decoded->data->user_name)->first();

            // check if user data exist
            if (!isset($user_data['jwt_token'])) {
                $data['error']="LogOut, Please Login";
                $data['message']="Someting went Worng";
                return response()->error($data,404);
            }else{
                request()->merge(['user_data'=>$user_data,'decoded_data'=>$decoded]);
                return $next($request);
            }
        }catch(\Exception $ex){
            $data['error']=$ex->getMessage();
            $data['message']="Someting went Worng";
            return response()->error($data,404);
        }
    }
}
