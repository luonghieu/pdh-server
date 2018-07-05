<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => ':attributeは、必ず指定してください。',
            'email.email' => ':attributeは、有効なメールアドレス形式で指定してください。',
            'email.max' => ':attributeは、:max文字以下にしてください。',
            'password.required' => ':attributeは、必ず指定してください。',
            'password.min' => ':attributeは、:min文字以上にしてください。',
        ];
    }
}
