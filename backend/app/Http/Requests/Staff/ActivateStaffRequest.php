<?php

namespace App\Http\Requests\Staff;

use App\Enums\StaffRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = $this->user();
        $subject = $this->route('staff');

        return $actor instanceof User
            && $subject instanceof User
            && $actor->can('activate', $subject);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'roles' => ['required', 'array', 'list', 'min:1'],
            'roles.*' => [
                'required',
                'string',
                'distinct:strict',
                Rule::enum(StaffRole::class),
            ],
        ];

        foreach ([
            'id',
            'user_id',
            'actor_id',
            'name',
            'email',
            'password',
            'password_confirmation',
            'role',
            'is_active',
            'email_verified_at',
            'deactivated_at',
            'deactivated_by',
            'deactivation_reason',
        ] as $field) {
            $rules[$field] = ['missing'];
        }

        return $rules;
    }
}
