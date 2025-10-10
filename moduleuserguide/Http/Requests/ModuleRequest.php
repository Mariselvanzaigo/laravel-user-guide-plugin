<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $module = $this->route('user_guide_module'); 
        $moduleId = $module ? $module->id : null;
        return [
            'name' => 'required|string|max:256|unique:user_guide_modules,name,' . $moduleId. ',id',
        ];
    }
}
