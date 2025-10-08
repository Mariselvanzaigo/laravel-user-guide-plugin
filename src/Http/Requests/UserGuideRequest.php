<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserGuideRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules() {
        $userGuideId = $this->route('user_guide')?->id ?? null;

        return [
            'module_id' => 'required|exists:modules,id',
            'name' => [
                'required',
                'string',
                'max:256',
                Rule::unique('user_guides')
                    ->where(fn($query) => $query->where('module_id', $this->module_id))
                    ->ignore($userGuideId),
            ],
            'description' => 'nullable|string|max:2000',
            'files.*' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,pdf,doc,docx,mp4',
            'urls.*' => 'nullable|url'
        ];
    }

    public function messages() {
        return [
            'module_id.required' => 'Select a module.',
            'name.required' => 'Enter user guide name.',
            'name.max' => 'Maximum 256 characters allowed.',
            'name.unique' => 'This name already exists for the selected module.',
            'description.max' => 'Maximum 2000 characters allowed.',
            'files.*.mimes' => 'Allowed file types: jpg, jpeg, png, pdf, doc, docx, mp4.',
            'files.*.max' => 'Each file must not exceed 20MB.',
            'urls.*.url' => 'Enter a valid URL (https://...)'
        ];
    }
}

