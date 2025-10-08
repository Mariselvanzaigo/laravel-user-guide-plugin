<?php
namespace ModuleUserGuide\Http\Controllers;

use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Http\Requests\ModuleRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 

class ModuleController extends Controller
{
    protected $layout = 'layouts.app';
    public function index() {
        $modules = Module::paginate(10);
        return view('moduleuserguide::modules.index', [
            'modules' => $modules,
            'layout' => $this->layout
        ]);
    }

    public function create() {
        return view('moduleuserguide::modules.create', [
            'layout' => $this->layout
        ]);
    }

    public function store(ModuleRequest $request) {
        Module::create($request->validated());
        return redirect()->route('modules.index')->with('success','Module created successfully!');
    }

    public function edit(Module $module) {
        return view('moduleuserguide::modules.edit', [
            'module' => $module,
            'layout' => $this->layout
        ]);
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
