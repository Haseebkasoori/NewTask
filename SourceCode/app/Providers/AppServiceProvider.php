<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('old_password',
        function($attribute, $value, $parameters, $validator){
            if(!empty(request()->user_data->password)){
                if (Hash::check($value, request()->user_data->password)) {
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        },"Invalid Credentionl!!");

        Validator::extend('valid_image',
            function($attribute, $value, $parameters, $validator){


                if(!empty($value)){
                // $img = preg_replace('/^data:image\/\w+;base64,/', '', $value);
                $type = explode(';', $value)[0];
                $type = explode('/', $type)[1];
                    if($type=='png' or $type=='jpeg' or $type=='bmp' or $type=='gif' or $type=='jpg' or $type=='svg' or $type=='webp' ){
                        return true;
                    }
                }elseif(empty($value)){
                    return true;
                }else
                return false;
            }
            ,"The selected :attribute must be png,jpeg,bmp,gif,jpg,svg or webp !!");

        Response::macro('success',function($data,$status_code){
            http_response_code($status_code);
            return response()->json([
                'success' => true,
                'error'   => false,
                'message' => $data['message'],
                'data'    => $data['data'],
            ],$status_code);

        });

        Response::macro('error',function($data, $status_code){
            http_response_code($status_code);
            return response()->json([
                'success' => false,
                'error'   => true,
                'message' => $data['message'],
                'data'    => null,
            ],$status_code);

        });

    }
}
