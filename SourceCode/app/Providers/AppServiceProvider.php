<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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

        Response::macro('success',function($data,$status_code){
            return response()->json([
                'success' => true,
                'message' => $data['message'],
                'data'    => $data['data'],
            ],$status_code);

        });

        Response::macro('error',function($data, $status_code){
            return response()->json([
                'success' => false,
                'message' => $data['message'],
                'error'   => $data['error'],
            ],$status_code);

        });
    }
}
