<?php
namespace ModuleUserGuide\Http\Controllers;

use ModuleUserGuide\Models\UserGuide;
use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Http\Requests\UserGuideRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserGuideController extends Controller
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
        $query = UserGuide::with('module')->orderByDesc('id');
        $userGuides = $query->paginate(10);

        return view('moduleuserguide::userguides.index', [
            'userGuides' => $userGuides,
            'layout' => $this->layout
        ]);
    }

    public function show(Request $request)
    {
        $modules = Module::with(['userGuides' => fn($q) => $q->orderByDesc('created_at')])->get();

        if ($modules->isEmpty()) {
            return view('moduleuserguide::userguides.show', [
                'modules' => collect(),
                'selectedModule' => null,
            ]);
        }

        $selectedModuleId = $request->get('module_id') ?? $modules->first()->id;

        if (!$request->has('module_id')) {
            return redirect()->to($this->prefixedRoute('user-guides.show', ['module_id' => $selectedModuleId]));
        }

        $selectedModule = $modules->firstWhere('id', $selectedModuleId) ?? $modules->first();

        return view('moduleuserguide::userguides.show', [
            'modules' => $modules,
            'selectedModule' => $selectedModule,
        ]);
    }

    public function create()
    {
        $modules = Module::all();
        return view('moduleuserguide::userguides.create', [
            'modules' => $modules,
            'layout' => $this->layout,
        ]);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $path = $file->store('user-guides/images', 'public');
            $url = Storage::url($path);

            return response()->json([
                'uploaded' => 1,
                'fileName' => $file->getClientOriginalName(),
                'url' => $url
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => ['message' => 'No file uploaded.']
        ]);
    }

    public function store(UserGuideRequest $request)
    {
        $data = $request->validated();
        $data['description'] = $request->input('description');

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('user-guides', 'public');
                $files[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
        }

        $data['files'] = $files;
        $data['urls'] = array_filter($request->input('urls', []), fn($url) => !empty($url));

        $userGuide = UserGuide::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $this->prefixedRoute('user-guides.index'),
                'message' => 'User Guide created successfully!',
            ]);
        }

        return redirect()->to($this->prefixedRoute('user-guides.index'))
            ->with('success', 'User Guide created successfully!');
    }

    public function edit(UserGuide $userGuide)
    {
        $modules = Module::all();
        return view('moduleuserguide::userguides.edit', [
            'userGuide' => $userGuide,
            'modules' => $modules,
            'layout' => $this->layout
        ]);
    }

    public function update(UserGuideRequest $request, UserGuide $userGuide)
    {
        $data = $request->validated();
        $data['description'] = $request->input('description');

        $files = $userGuide->files ?? [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('user-guides', 'public');
                $files[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
        }

        if ($request->filled('delete_files')) {
            foreach ($request->delete_files as $deletePath) {
                foreach ($files as $key => $fileData) {
                    if ($fileData['path'] === $deletePath) {
                        Storage::disk('public')->delete($fileData['path']);
                        unset($files[$key]);
                    }
                }
            }
            $files = array_values($files);
        }

        $data['files'] = array_values($files);
        $data['urls'] = array_filter($request->input('urls', []), fn($url) => !empty($url));

        $userGuide->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $this->prefixedRoute('user-guides.index'),
                'message' => 'User Guide updated successfully!',
            ]);
        }

        return redirect()->to($this->prefixedRoute('user-guides.index'))
            ->with('success', 'User Guide updated successfully!');
    }

    public function destroy(UserGuide $userGuide)
    {
        if ($userGuide->files) {
            foreach ($userGuide->files as $file) {
                Storage::delete($file);
            }
        }

        $userGuide->delete();

        return redirect()->to($this->prefixedRoute('user-guides.index'))
            ->with('success', 'User Guide deleted successfully!');
    }
}
