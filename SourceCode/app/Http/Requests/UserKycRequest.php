<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserKycRequest extends FormRequest
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
            'profile_image' => 'string|valid_image',
            'utility_bill_image' => 'string|valid_image',
            'cnic_image' => 'string|valid_image',
            'consignment' => 'required|in:true',

        ];
    }
    public function failedValidation(Validator $validator)
    {
        $data['message']=$validator->errors();
        throw new HttpResponseException(response()->error($data, 400));
    }
}
