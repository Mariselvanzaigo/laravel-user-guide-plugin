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
            <div id="file-list"></div>
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
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('{{ $formId }}');
    const fileInput = form.querySelector('#files');
    const fileList = form.querySelector('#file-list');
    const allowedTypes = [
        'image/jpeg','image/png','image/jpg','application/pdf',
        'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','video/mp4'
    ];

    // --- Validate a field ---
    const validateField = (input) => {
        const errorEl = document.getElementById(`${input.id}_error`);
        if (!errorEl) return true;
        errorEl.textContent = '';
        input.classList.remove('is-invalid');

        if (input.required && !input.value.trim()) {
            errorEl.textContent = 'This field is required.';
            input.classList.add('is-invalid');
            return false;
        }
        if (input.id === 'name' && input.value.length > 256) {
            errorEl.textContent = 'Maximum 256 characters allowed.';
            input.classList.add('is-invalid');
            return false;
        }
        if (input.id === 'description' && input.value.length > 2000) {
            errorEl.textContent = 'Maximum 2000 characters allowed.';
            input.classList.add('is-invalid');
            return false;
        }
        if (input.name === 'urls[]' && input.value.trim() && !/^https?:\/\/[^\s]+$/.test(input.value.trim())) {
            document.getElementById('urls_error').textContent = 'Enter valid URLs (https://...)';
            input.classList.add('is-invalid');
            return false;
        }
        return true;
    };

    // --- Field-level validation ---
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', () => validateField(input));
        input.addEventListener('change', () => validateField(input));
    });

    // --- File validation & preview ---
    fileInput?.addEventListener('change', () => {
        fileList.innerHTML = '';
        document.getElementById('files_error').textContent = '';
        Array.from(fileInput.files).forEach(file => {
            if (!allowedTypes.includes(file.type)) {
                document.getElementById('files_error').textContent = 'Invalid file type detected.';
                fileInput.classList.add('is-invalid');
            } else if (file.size > 20 * 1024 * 1024) {
                document.getElementById('files_error').textContent = 'File exceeds 20MB size limit.';
                fileInput.classList.add('is-invalid');
            } else {
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center justify-content-between border rounded p-2 mb-1';
                div.innerHTML = `
                    <span>${file.name}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-file">Delete</button>`;
                div.querySelector('.remove-file').addEventListener('click', () => {
                    div.remove();
                    const dataTransfer = new DataTransfer();
                    Array.from(fileInput.files).forEach(f => { if (f.name !== file.name) dataTransfer.items.add(f); });
                    fileInput.files = dataTransfer.files;
                });
                fileList.appendChild(div);
            }
        });
    });

    // --- Add URL ---
    form.querySelector('#add-url').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'url';
        input.name = 'urls[]';
        input.className = 'form-control mb-2';
        input.placeholder = 'https://example.com';
        input.addEventListener('input', () => validateField(input));
        form.querySelector('#url-fields').appendChild(input);
    });

    // --- Remove existing files (for edit form) ---
    form.querySelectorAll('.remove-existing-file')?.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const li = e.target.closest('li');
            li.remove();
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_files[]';
            input.value = li.dataset.file;
            form.appendChild(input);
        });
    });

    // --- Submit validation ---
    form.addEventListener('submit', (e) => {
        let valid = true;
        form.querySelectorAll('input, select, textarea').forEach(input => {
            if (!validateField(input)) valid = false;
        });
        if (fileInput && fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(file => {
                if (!allowedTypes.includes(file.type) || file.size > 20 * 1024 * 1024) valid = false;
            });
        }
        if (!valid) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});
</script>
@endpush
@endsection
