@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>Edit User Guide</h2>

    <form id="userGuideForm" action="{{ route('user-guides.update', $userGuide) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Module Select --}}
        <div class="form-group mb-3">
            <label for="module_id">Select Module <span class="text-danger">*</span></label>
            <select name="module_id" id="module_id" class="form-control" required>
                <option value="">-- Select Module --</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $userGuide->module_id == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger" id="module_id_error"></span>
        </div>

        {{-- User Guide Name --}}
        <div class="form-group mb-3">
            <label for="name">User Guide Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $userGuide->name) }}" maxlength="256" required>
            <span class="text-danger" id="name_error"></span>
        </div>

        {{-- Description --}}
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" maxlength="2000">{{ old('description', $userGuide->description) }}</textarea>
            <span class="text-danger" id="description_error"></span>
        </div>

        {{-- File Upload --}}
        <div class="form-group mb-3">
            <label for="files">Uploaded Files</label>
            <ul id="file-preview" class="mt-2">
                @if($userGuide->files)
                    @foreach($userGuide->files as $file)
                        <li data-existing-file="{{ $file }}">
                            <a href="{{ Storage::url($file) }}" target="_blank">{{ basename($file) }}</a>
                            <button type="button" class="btn btn-sm btn-danger ms-2 remove-existing-file">Delete</button>
                        </li>
                    @endforeach
                @endif
            </ul>
            <input type="file" name="files[]" id="files" class="form-control" multiple>
            <span class="text-danger" id="files_error"></span>
        </div>

        {{-- File URLs --}}
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
            <span class="text-danger" id="urls_error"></span>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userGuideForm');
    const fileInput = document.getElementById('files');
    const filePreview = document.getElementById('file-preview');

    // Add another URL field
    document.getElementById('add-url').addEventListener('click', function() {
        const container = document.getElementById('url-fields');
        const input = document.createElement('input');
        input.type = 'url';
        input.name = 'urls[]';
        input.className = 'form-control mb-2';
        input.placeholder = 'https://example.com';
        container.appendChild(input);
    });

    // Preview newly selected files
    fileInput.addEventListener('change', function() {
        Array.from(fileInput.files).forEach((file, index) => {
            const li = document.createElement('li');
            li.textContent = file.name + ' ';
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.textContent = 'Delete';
            delBtn.className = 'btn btn-sm btn-danger ms-2';
            delBtn.addEventListener('click', function() {
                const dt = new DataTransfer();
                Array.from(fileInput.files).forEach((f, i) => {
                    if(i !== index) dt.items.add(f);
                });
                fileInput.files = dt.files;
                li.remove();
            });
            li.appendChild(delBtn);
            filePreview.appendChild(li);
        });
    });

    // Delete existing uploaded files
    document.querySelectorAll('.remove-existing-file').forEach(btn => {
        btn.addEventListener('click', function() {
            const li = this.closest('li');
            li.remove();
            // optionally, create a hidden input to mark for deletion on backend
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_files[]';
            input.value = li.dataset.existingFile;
            form.appendChild(input);
        });
    });

    // Frontend Validation
    form.addEventListener('submit', function(e) {
        let valid = true;
        ['module_id_error','name_error','description_error','files_error','urls_error'].forEach(id => document.getElementById(id).textContent='');

        // Module required
        if(!form.module_id.value) {
            document.getElementById('module_id_error').textContent = 'Module is required';
            valid = false;
        }

        // Name required and max 256
        if(!form.name.value.trim()) {
            document.getElementById('name_error').textContent = 'User Guide name is required';
            valid = false;
        } else if(form.name.value.length > 256) {
            document.getElementById('name_error').textContent = 'Name cannot exceed 256 characters';
            valid = false;
        }

        // Description max 2000
        if(form.description.value.length > 2000) {
            document.getElementById('description_error').textContent = 'Description too long';
            valid = false;
        }

        // File validation
        Array.from(fileInput.files).forEach(file => {
            const allowedTypes = ['image/jpeg','image/png','image/jpg','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','video/mp4'];
            if(!allowedTypes.includes(file.type)) {
                document.getElementById('files_error').textContent = 'Invalid file type';
                valid = false;
            }
            if(file.size > 20*1024*1024) {
                document.getElementById('files_error').textContent = 'File size exceeds 20MB';
                valid = false;
            }
        });

        // URLs validation
        Array.from(form.querySelectorAll('input[name="urls[]"]')).forEach(urlInput => {
            if(urlInput.value && !/^https?:\/\/[^\s]+$/.test(urlInput.value)) {
                document.getElementById('urls_error').textContent = 'Invalid URL';
                valid = false;
            }
        });

        if(!valid) e.preventDefault();
    });
});
</script>
@endpush
@endsection
