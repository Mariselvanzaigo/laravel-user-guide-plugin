<?php
namespace ModuleUserGuide\Http\Controllers;

use ModuleUserGuide\Models\UserGuide;
use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Http\Requests\UserGuideRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller; 

class UserGuideController extends Controller
{
    protected $layout = 'layouts.app';

    // Controller
    public function index()
    {
        $query = UserGuide::with('module')->orderByDesc('id');


        // Paginate and append current query params
        $userGuides = $query->paginate(10);

        return view('moduleuserguide::userguides.index', [
            'userGuides' => $userGuides,
            'layout' => $this->layout
        ]);
    }


    public function create() {
        // $this->authorize('create', UserGuide::class);
        $modules = Module::all();
        return view('moduleuserguide::userguides.create', [
            'modules' => $modules,
            'layout' => $this->layout
        ]);
    }

    public function store(UserGuideRequest $request)
    {
        $data = $request->validated();

        // Handle file uploads on public disk
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
        $data['urls'] = $request->urls ?? [];

        $userGuide = UserGuide::create($data);

        // Return JSON for AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('user-guides.index'),
                'message' => 'User Guide created successfully!',
            ]);
        }

        return redirect()->route('user-guides.index')
                        ->with('success', 'User Guide created successfully!');
    }




    public function edit(UserGuide $userGuide) {
        // $this->authorize('update', $userGuide);
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

        $files = $userGuide->files ?? [];

        // Handle new file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('user-guides', 'public');
                $files[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
        }

        // Handle deleted files
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
        $data['urls'] = $request->urls ?? [];

        $userGuide->update($data);

        // Return JSON if AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('user-guides.index'),
                'message' => 'User Guide updated successfully!',
            ]);
        }

        return redirect()->route('user-guides.index')
                        ->with('success', 'User Guide updated successfully!');
    }



    public function destroy(UserGuide $userGuide) {
        // $this->authorize('delete', $userGuide);

        if($userGuide->files){
            foreach($userGuide->files as $file){
                Storage::delete($file);
            }
        }
        $userGuide->delete();
        return redirect()->route('user-guides.index')->with('success','User Guide deleted successfully!');
    }
}
