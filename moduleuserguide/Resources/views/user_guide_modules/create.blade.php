@php
if (view()->exists('larasnap::layouts.app')) {
    $layoutToUse = 'larasnap::layouts.app';
} else {
    $layoutToUse = $layout ?? 'layouts.app';
}
@endphp
@extends($layoutToUse)

@section('content')
<div class="container">
    <h2>Create Module</h2>
    {{-- @can('create', \ModuleUserGuide\Models\Module::class) --}}
    <form action="{{ route('user_guide_modules.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Module Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required maxlength="256">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button class="btn btn-success">Create</button>
        <a href="{{ route('user_guide_modules.index') }}" class="btn btn-secondary">Back</a>
    </form>
    {{-- @else
        <div class="alert alert-danger">You do not have permission to create modules.</div>
    @endcan --}}
</div>
@endsection
