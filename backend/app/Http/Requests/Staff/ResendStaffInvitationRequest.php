<?php

namespace App\Http\Requests\Staff;

use App\Enums\StaffRole;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResendStaffInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = $this->user();
        $invitation = $this->route('invitation');

        return $actor instanceof User
            && $invitation instanceof StaffInvitation
            && $actor->can('resend', $invitation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $invitation = $this->route('invitation');

        $includesAdministrator = $invitation instanceof StaffInvitation
            && in_array(
                StaffRole::Administrator,
                $invitation->assignedRoles(),
                true,
            );

        $rules = [
            'administrator_confirmation' => $includesAdministrator
                ? ['required', 'boolean', 'accepted']
                : ['sometimes', 'boolean'],
        ];

        foreach ([
            'id',
            'invitation_id',
            'user_id',
            'actor_id',
            'email',
            'role',
            'roles',
            'name',
            'password',
            'password_confirmation',
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
