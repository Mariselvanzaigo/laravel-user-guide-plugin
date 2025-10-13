<?php
namespace ModuleUserGuide\Http\Controllers;

use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Http\Requests\ModuleRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ModuleController extends Controller
{
    protected $layout = 'layouts.app';

    // Helper to generate route with dynamic prefix
    protected function prefixedRoute($name, $params = [])
    {
        $prefix = request()->segment(1) ?? 'default';
        return route($prefix . '.module-user-guide.' . $name, $params);
    }

    public function index()
    {
        // $this->authorize('index', Module::class);
        $modules = Module::paginate(6);
        return view('moduleuserguide::user_guide_modules.index', [
            'modules' => $modules,
            'layout' => $this->layout
        ]);
    }

    public function create()
    {
        // $this->authorize('create', Module::class);
        return view('moduleuserguide::user_guide_modules.create', [
            'layout' => $this->layout
        ]);
    }

    public function store(ModuleRequest $request)
    {
        // $this->authorize('store', Module::class);
        Module::create($request->validated());

        return redirect()->to($this->prefixedRoute('user_guide_modules.index'))
            ->with('success', 'Module created successfully!');
    }

    public function edit(Module $user_guide_module)
    {
        // $this->authorize('edit', $module);
        return view('moduleuserguide::user_guide_modules.edit', [
            'module' => $user_guide_module,
            'layout' => $this->layout
        ]);
    }

    public function update(ModuleRequest $request, Module $user_guide_module)
    {
        // $this->authorize('update', $module);
        $user_guide_module->update($request->validated());

        return redirect()->to($this->prefixedRoute('user_guide_modules.index'))
            ->with('success', 'Module updated successfully!');
    }

    public function destroy(Module $user_guide_module)
    {
        // $this->authorize('delete', $module);
        $user_guide_module->delete();

        return redirect()->to($this->prefixedRoute('user_guide_modules.index'))
            ->with('success', 'Module deleted successfully!');
    }
}
