<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserGuideRequest extends FormRequest
{
    public const MAX_DESC_CHARS = 20000;
    public function authorize() { return true; }

    public function rules() {
        $userGuideId = $this->route('user_guide')?->id ?? null;

        return [
            'module_id' => 'required|exists:user_guide_modules,id',
            'name' => [
                'required',
                'string',
                'max:256',
                Rule::unique('user_guides')
                    ->where(fn($query) => $query->where('module_id', $this->module_id))
                    ->ignore($userGuideId),
            ],
            'description' => 'nullable|string|max:2000',
            'files.*' => 'nullable|file|max:20480|mimetypes:application/pdf,image/jpeg,image/png,video/mp4',
            'urls.*' => 'nullable|url'
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
        $desc = $this->input('description', '');
        // Remove HTML tags and decode entities to count real characters
        $plain = trim(strip_tags(html_entity_decode($desc)));


        if ($plain !== '' && mb_strlen($plain) > self::MAX_DESC_CHARS) {
        $validator->errors()->add('description', "Description must be at most " . self::MAX_DESC_CHARS . " characters (plain-text). Please shorten the content.");
        }
        });
    }
    public function wantsJson(): bool
    {
        // Force validation to return JSON for AJAX requests
        return $this->ajax() || parent::wantsJson();
    }
    public function messages() {
        return [
            'module_id.required' => 'Select a module.',
            'name.required' => 'Enter user guide name.',
            'name.max' => 'Maximum 256 characters allowed.',
            'name.unique' => 'This name already exists for the selected module.',
            'description.max' => 'Maximum 2000 characters allowed.',
            'files.*.mimes' => 'Allowed file types: jpg, jpeg, png, pdf, mp4.',
            'files.*.max' => 'Each file must not exceed 20MB.',
            'urls.*.url' => 'Enter a valid URL (https://...)'
        ];
    }
}

