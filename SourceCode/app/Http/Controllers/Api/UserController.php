<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\GetUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRestRequest;
use App\Http\Requests\UserDeleteRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserSaveRequest;
use App\Http\Requests\VarifyEmailReqeust;
use App\Http\Resources\UserResource;
use App\Jobs\EmailVarificationMailJob;
use App\Jobs\ForgotPasswordJob;
use App\Models\User;
use App\Services\JwtAuthentication;
use Exception;
use Illuminate\Support\Facades\Request;


class UserController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
    */
    public function register(UserSaveRequest $request)
    {
        try{
            $data=$request->validated();
            $user = new User();
            foreach ($data as $key => $value) {
                $user->$key = $value;
            }
            $user->password =  bcrypt($data['password']);
            $email_varified_token = md5($data['user_name']);
            $user->email_varified_token= $email_varified_token;
            $user->save();

            // data creation for email
            $details['link']=url('api/emailConfirmation/'.$data['email'].'/'.$email_varified_token);
            $details['user_name']=$data['user_name'];
            $details['email']=$data['email'];

            //send verification mail
            try{
                dispatch(new EmailVarificationMailJob($details));

            }catch(Exception $ex){
                info($ex->getMessage());
            }

            // data creation for response
            $response_data['data']=null;
            $response_data['message']=strtoupper($user->user_name).', Please check your mail ('.$user->email.') for Email Varification';
            return response()->success($response_data,200);

        }catch(Exception $ex){
            info($ex->getMessage());
            $response_data['error']=null;
            $response_data['message']="Someting went Worng";
            return response()->error($response_data, 500);
        }
    }


    /**
     * Verify api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyingEmail(VarifyEmailReqeust $reqeust,$email,$email_varified_token)
    {
        try{

            $user = User::where("email",$email)->where('email_varified_token',$email_varified_token)->first();
            $user->email_varified_token= "";
            $user->email_verified_at= date('Y-m-d h:i:s');
            $user->save();

            // data creation for response
            $data['data']=Null;
            $data['message']=$user->user_name.' Your Account Has Been Verified';
            return response()->success($data,200);
        }catch(Exception $ex ){
            info($ex->getMessage());
            $response_data['error']=null;
            $response_data['message']="Someting went Worng Please Contact to Service Support or Try Again";
            return response()->error($response_data, 500);
        }
    }

}
