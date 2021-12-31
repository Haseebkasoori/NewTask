<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
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
            'user_name' => 'string|unique:users|max:100',
            'email' => 'email|unique:users|string|max:100',
            'gender' => "string|in:Male,Female,Other",
            'password'=> 'confirmed|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/', //must be at least 8 characters in length, at least one lowercase and uppercase letter,at least one digit and a special character
            'phone_number' => 'digits:11',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $data['message']=$validator->errors();
        throw new HttpResponseException(response()->error($data, 404));
    }
}
