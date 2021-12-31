<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TwoFactorAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'otp_mail' => 'required|string|exists:two_factor_auth,otp_mail',
            'otp_sms' => 'required|string|exists:two_factor_auth,otp_sms',
            'otp_authenticator' => 'required|string|exists:two_factor_auth,otp_authenticator',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $data['message']=$validator->errors();
        throw new HttpResponseException(response()->error($data, 400));
    }

}
