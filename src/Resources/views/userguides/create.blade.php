@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>Create User Guide</h2>

    <form id="userGuideForm" action="{{ route('user-guides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Module Select --}}
        <div class="form-group mb-3">
            <label for="module_id">Select Module <span class="text-danger">*</span></label>
            <select name="module_id" id="module_id" class="form-control" required>
                <option value="">-- Select Module --</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger" id="module_id_error"></span>
        </div>

        {{-- User Guide Name --}}
        <div class="form-group mb-3">
            <label for="name">User Guide Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" maxlength="256" required>
            <span class="text-danger" id="name_error"></span>
        </div>

        {{-- Description --}}
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" maxlength="2000">{{ old('description') }}</textarea>
            <span class="text-danger" id="description_error"></span>
        </div>

        {{-- File Upload --}}
        <div class="form-group mb-3">
            <label for="files">Upload Files (Images, Video, PDF, Docs, Max 20MB each)</label>
            <input type="file" name="files[]" id="files" class="form-control" multiple>
            <small class="text-muted">Allowed: jpg, jpeg, png, pdf, doc, docx, mp4. Max size: 20MB each</small>
            <ul id="file-preview" class="mt-2"></ul>
            <span class="text-danger" id="files_error"></span>
        </div>

        {{-- File URLs --}}
        <div class="form-group mb-3">
            <label for="urls">File URLs (Optional)</label>
            <div id="url-fields">
                <input type="url" name="urls[]" class="form-control mb-2" placeholder="https://example.com" value="{{ old('urls.0') }}">
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="add-url">Add Another URL</button>
            <span class="text-danger" id="urls_error"></span>
        </div>

        <button class="btn btn-success">Create</button>
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

    // Preview selected files
    fileInput.addEventListener('change', function() {
        filePreview.innerHTML = '';
        Array.from(fileInput.files).forEach((file, index) => {
            const li = document.createElement('li');
            li.textContent = file.name + ' ';
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.textContent = 'Delete';
            delBtn.className = 'btn btn-sm btn-danger ms-2';
            delBtn.addEventListener('click', function() {
                // Remove file from input
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

    // Frontend Validation
    form.addEventListener('submit', function(e) {
        let valid = true;

        // Clear previous errors
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
            if(file.size > 20*1024*1024) { // 20MB
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
