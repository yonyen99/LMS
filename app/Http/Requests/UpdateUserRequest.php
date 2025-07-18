<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => 'required|string|max:250',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:250',
                Rule::unique('users')->whereNull('deleted_at')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required',
            'images' => 'nullable|string',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->whereNull('deleted_at')->ignore($userId),
            ],
            'is_active' => 'nullable|boolean'
        ];
    }
}