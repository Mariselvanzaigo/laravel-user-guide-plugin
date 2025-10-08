@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>Edit User Guide</h2>
    <form action="{{ route('user-guides.update', $userGuide) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="module_id">Select Module</label>
            <select name="module_id" class="form-control" required>
                <option value="">-- Select Module --</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $userGuide->module_id == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                @endforeach
            </select>
            @error('module_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="name">User Guide Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $userGuide->name) }}" maxlength="256" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $userGuide->description) }}</textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="files">Uploaded Files</label>
            @if($userGuide->files)
                <ul>
                    @foreach($userGuide->files as $file)
                        <li>
                            <a href="{{ Storage::url($file) }}" target="_blank">{{ basename($file) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <input type="file" name="files[]" class="form-control" multiple>
            @error('files.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="urls">File URLs</label>
            @if($userGuide->urls)
                @foreach($userGuide->urls as $url)
                    <input type="url" name="urls[]" class="form-control mb-1" value="{{ $url }}">
                @endforeach
            @endif
            <input type="url" name="urls[]" class="form-control mb-1" placeholder="https://example.com">
            @error('urls.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
