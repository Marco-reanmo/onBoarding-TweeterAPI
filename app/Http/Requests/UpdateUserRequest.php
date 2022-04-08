<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'forename' => ['min:3', 'max:255', 'alpha'],
            'surname' => ['min:3', 'max:255', 'alpha'],
            'profile_picture' => ['image'],
            'email' => ['max:255', 'email', Rule::unique('users', 'email')->ignore($this->user())],
            'old_password' => [Rule::requiredIf(function () {
                return $this->request->has('password');
            }), 'current_password'],
            'password' => ['confirmed', Password::defaults()],
        ];
    }
}
