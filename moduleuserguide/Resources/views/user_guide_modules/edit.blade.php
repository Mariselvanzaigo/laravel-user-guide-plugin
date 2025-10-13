@php
if (view()->exists('larasnap::layouts.app')) {
    $layoutToUse = 'larasnap::layouts.app';
} else {
    $layoutToUse = $layout ?? 'layouts.app';
}

// Dynamic prefix based on first URL segment
$prefix = request()->segment(1) ?? 'default';
@endphp

@extends($layoutToUse)

@section('content')
<style>
    .container_plugin_module {
        height: auto;
        min-height: 700px;
    }
</style>

<div class="container container_plugin_module m-4">
    <div class="row">
        <!-- Back Button -->
        <div>
            <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.index') }}" 
               class="btn btn-secondary m-1" 
               style="padding:3px 13px 3px 12px;">
               <i class="fas fa-arrow-left me-1"></i>
            </a>
        </div>
        <h2>Edit Module</h2>
    </div>

    {{-- @can('update', $module) --}}
    <form action="{{ route($prefix . '.module-user-guide.user_guide_modules.update', ['user_guide_module' => $module->id]) }}" 
          method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="name">Module Name</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name', $module->name) }}" 
                   required maxlength="256">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.index') }}" 
           class="btn btn-secondary">Back</a>
    </form>
    {{-- @else
        <div class="alert alert-danger">You do not have permission to edit this module.</div>
    @endcan --}}
</div>
@endsection
