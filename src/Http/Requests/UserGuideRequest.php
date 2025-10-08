<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGuideRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules() {
        // Get current user guide ID for updates
        $userGuideId = $this->route('user_guide')?->id ?? null;

        return [
            'module_id' => 'required|exists:modules,id',
            'name' => [
                'required',
                'string',
                'max:256',
                // Unique per module
                \Illuminate\Validation\Rule::unique('user_guides')
                    ->where(function ($query) {
                        return $query->where('module_id', $this->module_id);
                    })
                    ->ignore($userGuideId),
            ],
            'description' => 'nullable|string',
            'files.*' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,pdf,doc,docx,mp4',
            'urls.*' => 'nullable|url'
        ];
    }
}
