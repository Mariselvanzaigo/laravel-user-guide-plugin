@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>Edit User Guide</h2>

    @can('update', $userGuide)
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
                            @can('delete', $userGuide)
                                <!-- Optionally add delete button for each file -->
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
            <input type="file" name="files[]" class="form-control" multiple>
            @error('files.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="urls">File URLs</label>
            <div id="url-fields">
                @if($userGuide->urls)
                    @foreach($userGuide->urls as $url)
                        <input type="url" name="urls[]" class="form-control mb-2" value="{{ $url }}">
                    @endforeach
                @endif
                <input type="url" name="urls[]" class="form-control mb-2" placeholder="https://example.com">
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="add-url">Add Another URL</button>
            @error('urls.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
        <div class="alert alert-danger">You do not have permission to edit this user guide.</div>
    @endcan
</div>

@push('scripts')
<script>
    document.getElementById('add-url').addEventListener('click', function() {
        let container = document.getElementById('url-fields');
        let input = document.createElement('input');
        input.type = 'url';
        input.name = 'urls[]';
        input.className = 'form-control mb-2';
        input.placeholder = 'https://example.com';
        container.appendChild(input);
    });
</script>
@endpush
@endsection
