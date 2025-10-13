@php
if (view()->exists('larasnap::layouts.app')) {
    $layoutToUse = 'larasnap::layouts.app';
} else {
    $layoutToUse = $layout ?? 'layouts.app';
}

// Dynamic prefix based on first segment
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
        <h2>Create Module</h2>
    </div>

    {{-- @can('create', \ModuleUserGuide\Models\Module::class) --}}
    <form id="moduleForm" 
          action="{{ route($prefix . '.module-user-guide.user_guide_modules.store') }}" 
          method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="name">Module Name</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name') }}" maxlength="256">
            <span id="name-error" class="text-danger"></span>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.index') }}" 
           class="btn btn-secondary">Back</a>
    </form>
    {{-- @else
        <div class="alert alert-danger">You do not have permission to create modules.</div>
    @endcan --}}
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('moduleForm');
    if (!form) return; // safety check

    const nameInput = document.getElementById('name');
    const errorSpan = document.getElementById('name-error');

    function validateName() {
        const value = nameInput.value.trim();
        if (value === '') {
            errorSpan.textContent = 'Module name is required.';
            return false;
        } else if (value.length > 256) {
            errorSpan.textContent = 'Module name cannot exceed 256 characters.';
            return false;
        } else {
            errorSpan.textContent = '';
            return true;
        }
    }

    nameInput.addEventListener('input', validateName);

    form.addEventListener('submit', function (e) {
        if (!validateName()) {
            e.preventDefault();
            nameInput.focus();
        }
    });
});
</script>
@endsection
