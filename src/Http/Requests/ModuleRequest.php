<?php
namespace ModuleUserGuide\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules() {
        return ['name' => 'required|string|max:256'];
    }
}
