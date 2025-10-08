<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGuideRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules() {
        return [
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:256',
            'description' => 'nullable|string',
            'files.*' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,pdf,doc,docx,mp4',
            'urls.*' => 'nullable|url'
        ];
    }
}
