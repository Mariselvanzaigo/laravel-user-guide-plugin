<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $moduleId = $this->route('module')?->id ?? null;

        return [
            'name' => 'required|string|max:256|unique:modules,name,' . $moduleId,
        ];
    }
}
