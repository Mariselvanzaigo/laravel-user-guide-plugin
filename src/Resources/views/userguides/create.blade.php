@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create User Guide</h2>
    <form action="{{ route('user-guides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="module_id">Select Module</label>
            <select name="module_id" class="form-control" required>
                <option value="">-- Select Module --</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                @endforeach
            </select>
            @error('module_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="name">User Guide Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" maxlength="256" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="files">Upload Files (Images, Video, PDF, Docs, Max 20MB each)</label>
            <input type="file" name="files[]" class="form-control" multiple>
            @error('files.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="urls">File URLs (Optional)</label>
            <input type="url" name="urls[]" class="form-control mb-2" placeholder="https://example.com">
            @error('urls.*') <span class="text-danger">{{ $message }}</span> @enderror
            <small class="text-muted">Add multiple URLs separated by commas</small>
        </div>

        <button class="btn btn-success">Create</button>
        <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
