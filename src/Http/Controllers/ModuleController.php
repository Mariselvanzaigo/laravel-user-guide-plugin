<?php

namespace LaravelUserGuide\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LaravelUserGuide\Models\Module;

class ModuleController extends Controller
{
    public function index() {
        \ = Module::paginate(10);
        return view('userguide::modules.index', compact('modules'));
    }

    public function create() {
        return view('userguide::modules.create');
    }

    public function store(Request \) {
        \->validate(['name'=>'required|max:256']);
        Module::create(['name'=>\->name]);
        return redirect()->route('modules.index')->with('success','Module created!');
    }
}
