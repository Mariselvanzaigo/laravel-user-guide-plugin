<?php

namespace LaravelUserGuide\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LaravelUserGuide\Models\UserGuide;
use LaravelUserGuide\Models\Module;

class UserGuideController extends Controller
{
    public function index() {
        \ = UserGuide::with('module')->paginate(10);
        return view('userguide::userguides.index', compact('guides'));
    }

    public function create() {
        \ = Module::all();
        return view('userguide::userguides.create', compact('modules'));
    }

    public function store(Request \) {
        \->validate([
            'module_id'=>'required|exists:modules,id',
            'name'=>'required|max:256'
        ]);
        UserGuide::create(\->only('module_id','name','description'));
        return redirect()->route('guides.index')->with('success','User Guide created!');
    }
}
