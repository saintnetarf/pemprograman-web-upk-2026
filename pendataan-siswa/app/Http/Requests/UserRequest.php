<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole(Role::Admin);
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['required', Rule::enum(Role::class)],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }
}

