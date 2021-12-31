<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use \Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users|string',
            'password'=> 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/', //must be at least 8 characters in length, at least one lowercase and uppercase letter,at least one digit and a special character
        ];
    }



    public function failedValidation(Validator $validator)
    {
        $data['error']=$validator->errors();
        $data['message']="Someting went Worng";
        throw new HttpResponseException(response()->error($data, 404));
    }
}
