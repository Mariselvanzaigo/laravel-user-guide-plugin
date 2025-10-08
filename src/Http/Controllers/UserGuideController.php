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
    public function index() {
        $userGuides = UserGuide::with('module')->paginate(10);
        return view('moduleuserguide::userguides.index', [
            'userGuides' => $userGuides,
            'layout' => $this->layout
        ]);
    }

    public function create() {
        $modules = Module::all();
        return view('moduleuserguide::userguides.create', [
            'modules' => $modules,
            'layout' => $this->layout
        ]);
    }

    public function store(UserGuideRequest $request) {
        $data = $request->validated();

        // handle file uploads
        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $files[] = $file->store('user-guides');
            }
        }
        $data['files'] = $files;
        $data['urls'] = $request->urls ?? [];

        UserGuide::create($data);
        return redirect()->route('user-guides.index')->with('success','User Guide created successfully!');
    }

    public function edit(UserGuide $userGuide) {
        $modules = Module::all();
        return view('moduleuserguide::userguides.edit', [
            'userGuide' => $userGuide,
            'modules' => $modules,
            'layout' => $this->layout
        ]);
    }

    public function update(UserGuideRequest $request, UserGuide $userGuide) {
        $data = $request->validated();

        $files = $userGuide->files ?? [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $files[] = $file->store('user-guides');
            }
        }
        $data['files'] = $files;
        $data['urls'] = $request->urls ?? [];

        $userGuide->update($data);
        return redirect()->route('user-guides.index')->with('success','User Guide updated successfully!');
    }

    public function destroy(UserGuide $userGuide) {
        // delete files
        if($userGuide->files){
            foreach($userGuide->files as $file){
                Storage::delete($file);
            }
        }
        $userGuide->delete();
        return redirect()->route('user-guides.index')->with('success','User Guide deleted successfully!');
    }

}
