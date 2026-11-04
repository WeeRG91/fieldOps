<?php

namespace App\Http\Requests\Invitations;

use App\Actions\Staff\ValidateStaffInvitation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InspectStaffInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'invitation_token' => [
                'bail',
                'required',
                'string',
                'size:64',
                'regex:/\A[0-9a-f]{64}\z/',
            ],
        ];

        foreach ([
            'id',
            'invitation_id',
            'user_id',
            'actor_id',
            'email',
            'role',
            'roles',
            'is_active',
            'email_verified_at',
            'invited_by',
            'accepted_at',
            'accepted_user_id',
            'revoked_at',
            'revoked_by',
            'superseded_at',
            'expires_at',
            'token_hash',
            'created_at',
            'updated_at',
        ] as $field) {
            $rules[$field] = ['missing'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_fill_keys([
            'invitation_token.required',
            'invitation_token.string',
            'invitation_token.size',
            'invitation_token.regex',
        ], ValidateStaffInvitation::UNAVAILABLE);
    }
}
