<?php
namespace ModuleUserGuide\Http\Controllers;

use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Http\Requests\ModuleRequest;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index() {
        $modules = Module::paginate(10);
        return view('moduleuserguide::modules.index', compact('modules'));
    }

    public function create() {
        return view('moduleuserguide::modules.create');
    }

    public function store(ModuleRequest $request) {
        Module::create($request->validated());
        return redirect()->route('modules.index')->with('success','Module created successfully!');
    }

    public function edit(Module $module) {
        return view('moduleuserguide::modules.edit', compact('module'));
    }

    public function update(ModuleRequest $request, Module $module) {
        $module->update($request->validated());
        return redirect()->route('modules.index')->with('success','Module updated successfully!');
    }

    public function destroy(Module $module) {
        $module->delete();
        return redirect()->route('modules.index')->with('success','Module deleted successfully!');
    }
}
