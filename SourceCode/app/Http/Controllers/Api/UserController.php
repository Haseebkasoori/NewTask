<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageUploading;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\GetUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRestRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserKycRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserSaveRequest;
use App\Http\Requests\VarifyEmailReqeust;
use App\Http\Resources\UserResource;
use App\Jobs\EmailVarificationMailJob;
use App\Jobs\ForgotPasswordMailJob;
use App\Jobs\LoginOtpJob;
use App\Models\TwoFactorAuth;
use App\Models\User;
use App\Models\UserKyc;
use App\Services\JwtAuthentication;
use Exception;
use Illuminate\Support\Facades\Request;
use Twilio\Rest\Client;



class UserController extends BaseController
{
    /**
     * Register api
     * @param \Illuminate\Http\Request
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
            return response()->error(['message'=>$ex->getMessage()], 500);
        }
    }

    /**
     * Register api
     *
     * @param \Illuminate\Http\Request
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
            $response_data['data']=Null;
            $response_data['message']=$user->user_name.' Your Account Has Been Verified';
            return response()->success($response_data,200);
        }catch(Exception $ex ){
            info($ex->getMessage());
            $response_data['message']=$ex->getMessage();
            return response()->error($response_data, 500);
        }
    }

    /**
     * ResetPassword api
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
    */
    public function ResetPassword(PasswordRestRequest $request)
    {
        $user = User::where('email',$request->email)->first();

        // creating a new password
        $digits    = array_flip(range('0', '9'));
        $lowercase = array_flip(range('a', 'z'));
        $uppercase = array_flip(range('A', 'Z'));
        $special   = array_flip(str_split('@$!%*#?&'));
        $combined  = array_merge($digits, $lowercase, $uppercase, $special);

        $password  = str_shuffle(array_rand($digits) .
                                array_rand($lowercase) .
                                array_rand($uppercase) .
                                array_rand($special) .
                                implode(array_rand($combined, rand(4, 8))));

        echo $password;
        // data creation for email
        $details['new_password']=$password;
        $details['user_name']=$user->user_name;
        $details['email']=$request->email;

        try{
            //send New Password mail
            dispatch(new ForgotPasswordMailJob($details));

            //save data in database
            $user->password =  Hash::make($password);
            $update_data=$user->update();

            if (!$update_data) {
                throw new Exception('Have some problem in update password please try again later');
            }else{
                // data creation for response
                $response_data['data']=Null;
                $response_data['message']=$user->user_name.' Please check your mail  '.$user->email.' for New Password';
                return response()->success($response_data,200);
            }

        }catch(\Exception $ex){
            $response_data['error']=$ex->getMessage();
            $response_data['message']="Someting went Worng";
            return response()->error($response_data,404);
        }

    }

    /**
     * Login api
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function Login(LoginRequest $request)
    {
        try{
            $user = User::where('email',$request->email)->first();
            if(!empty($user)){

                // Creating JWT token
                $details['otp_mail']=rand();
                $details['otp_sms']=rand();
                $details['user_name']=$user->user_name;
                $details['email']=$request->email;

                //send New Password mail
                dispatch(new LoginOtpJob($details['otp_mail']));

                $jwt_token=JwtAuthentication::createJwtToken($user);

                    //save data in database
                $TwoFactorAuth=new TwoFactorAuth();

                $TwoFactorAuth->otp_mail =  $details['otp_mail'];
                $TwoFactorAuth->otp_sms =  $details['otp_sms'];
                $TwoFactorAuth->otp_authenticator = rand();
                $TwoFactorAuth->User()->associate($user->id);
                $TwoFactorAuth->save();

                // data creation for response
                $response_data['message']=strtoupper($user->user_name).' Welcome to the Application please check your mail and number for Verification Token!!';
                $response_data['data']['token_type']="Bearer";
                $response_data['data']['login_token']=$jwt_token['token'];
                return response()->success($response_data,200);
            }else{

                throw new Exception("Invalid Credentionl !!");

            }
        }catch(Exception $ex){
            info($ex->getMessage());

            $response_data['message']=$ex->getMessage();
            return response()->error($response_data, 500);
        }
    }

    /**
     * Login api
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function TwoFactorAuth(Request $request)
    {
        try{
            $user=request()->user_data;
            if (!empty(request('two_fa'))) {
                // Creating JWT token
                $jwt_token=JwtAuthentication::createJwtToken($user);

                // save JWT token in DB
                $user->jwt_token= $jwt_token['token'];
                $user->update();
                // data creation for response
                $response_data['message']='Welcome to System!!!';
                $response_data['data']['token_type']="Bearer";
                $response_data['data']['Authenticaiton']=$jwt_token['token'];
                $response_data['data']['Usre_data']=new UserResource($user);

                return response()->success($response_data,200);
            }
        }catch(Exception $ex){
            info($ex->getMessage());
            $response_data['error']=$ex->getMessage();
            $response_data['message']="Someting went Worng";
            return response()->error($response_data, 500);
        }
    }


    /**
     * Update User request
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function UpdateUser(UserUpdateRequest $request)
    {
        try{
            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['user_name','phone_number','gender'])) {
                    request()->user_data->$key=$value;
                }
            }

            if(request()->user_data->update()){

                // data creation for response
                $response_data['data']['Usre_data']=new UserResource(request()->user_data);
                $response_data['message']='Updated Successfully !!!';
                return response()->success($response_data,200);

            }else{
                throw new Exception("Have Problem in Updation");
            }

        }catch(Exception $ex ){
            info($ex->getMessage());
            $response_data['error']=$ex->getMessage();
            $response_data['message']="Someting went Worng";
            return response()->error($response_data, 500);
        }
    }

    /**
     * Update Password api
     *
     * @return \Illuminate\Http\Response
    */
    public function updateUserPassword(UpdatePasswordRequest $request){
        try{
            $user=request()->user_data;
            $user->password=bcrypt(request('password'));
            $user->update();
            $response_data['data']['User_data']=new UserResource($user);
            $response_data['message']='Password Updated!!';
            return response()->success($response_data,200);
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);
        }
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function Logout(Request $request)
    {
        try{
            $user=request()->user_data;
            // remove jwt token from database
            $user->jwt_token=Null;
            $update_data=$user->update();
            if ($update_data) {

                // data creation for response
                $response_data['data']=Null;
                $response_data['message']='Logout Successfully';
                return response()->success($response_data,200);

            }else{
                // data creation for response
                throw new Exception("Have some Problem in Logout");

            }
        }catch(Exception $ex){
            $response_data['error']=$ex->getMessage();
            $response_data['message']="Someting went Worng";
            return response()->error($response_data,404);
        }
    }

    /**
     * Delete User api
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function DeleteUser(Request $request)
    {
        try{
            User::where('id',request()->user_data->id)->delete();
            // data creation for response
            $response_data['data']= null;
            $response_data['message']='User Deleted Successfully!!';
            return response()->success($response_data,200);
        }
        catch (\Exception $e) {
            $response_data['error']="Have some Problem in Deleting Account";
            $response_data['message']="Someting went Worng";
            return response()->error($response_data,404);
        }

    }


    /**
     * KYC User api
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function UserKYC(UserKycRequest $request)
    {
        try{

            \File::makeDirectory(public_path('storage/users/'.request()->decoded_data->data->id),0777,true);
            $kyc= new UserKyc();

            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['profile_image', 'utility_bill_image','cnic_image'])) {
                    $file_name=ImageUploading::imageUploading($value,'users',request()->decoded_data->data->id,$key);
                    $kyc->$key =$file_name;
                }
            }
            if(!empty(request('consignment'))){
                $kyc->consignment=request('consignment');
            }
            $kyc->User()->associate(request()->decoded_data->data->id);
            $kyc->save;
            $response_data['data']= null;
            $response_data['message']='Successfully Uploaded wait for Verificaiton!!';
            return response()->success($response_data,200);
        }catch (\Exception $e) {
            return response()->error(['message'=>"Have some Problem please Try Again"],404);
        }

    }



}
