<?php

namespace App\Http\Middleware;

use App\Models\UserKyc as ModelsUserKyc;
use Closure;
use Illuminate\Http\Request;

class CheckKYC
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
            $kyc=ModelsUserKyc::where('user_id',request()->decoded_data->data->id)->first();
            if(!empty($kyc->status)){
                if($kyc->status =='Pending'){
                    throw new \Exception('Please Wait for Verifiy your Documents');
                }else{
                    return $next($request);
                }
            }
        }catch(\Exception $ex){
            $data['message']=$ex->getMessage();
            return response()->error($data,404);
        }
    }
}
