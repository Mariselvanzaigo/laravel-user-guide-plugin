@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>Edit Module</h2>
    <form action="{{ route('modules.update', $module) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="name">Module Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $module->name) }}" required maxlength="256">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('modules.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
