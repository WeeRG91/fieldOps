<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = $this->profileRules($this->user()->id);

        $rules['email'][] = Rule::in([$this->user()->email]);
        $rules['role'] = ['missing'];
        $rules['roles'] = ['missing'];
        $rules['is_active'] = ['missing'];
        $rules['email_verified_at'] = ['missing'];
        $rules['deactivated_at'] = ['missing'];
        $rules['deactivated_by'] = ['missing'];
        $rules['deactivation_reason'] = ['missing'];

        return $rules;
    }
}
