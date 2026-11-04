<?php

namespace App\Http\Requests\Staff;

use App\Enums\StaffRole;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueStaffInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = $this->user();

        return $actor instanceof User
            && $actor->can('create', StaffInvitation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roles = $this->input('roles', []);

        $includesAdministrator = is_array($roles)
            && in_array(
                StaffRole::Administrator->value,
                $roles,
                true,
            );

        $rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
            'roles' => ['required', 'array', 'list', 'min:1'],
            'roles.*' => [
                'required',
                'string',
                'distinct:strict',
                Rule::enum(StaffRole::class),
            ],
            'administrator_confirmation' => $includesAdministrator
                ? ['required', 'boolean', 'accepted']
                : ['sometimes', 'boolean'],
        ];

        foreach ([
            'id',
            'invitation_id',
            'user_id',
            'actor_id',
            'name',
            'password',
            'password_confirmation',
            'role',
            'token',
            'invitation_token',
            'token_hash',
            'invited_by',
            'expires_at',
            'accepted_at',
            'accepted_user_id',
            'revoked_at',
            'revoked_by',
            'superseded_at',
            'is_active',
            'email_verified_at',
            'created_at',
            'updated_at',
        ] as $field) {
            $rules[$field] = ['missing'];
        }

        return $rules;
    }
}
