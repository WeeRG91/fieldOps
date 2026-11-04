<?php

namespace App\Http\Requests\Staff;

use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RevokeStaffInvitationRequest extends FormRequest
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
            && $actor->can('revoke', $invitation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_fill_keys([
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
            'administrator_confirmation',
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
        ], ['missing']);
    }
}
