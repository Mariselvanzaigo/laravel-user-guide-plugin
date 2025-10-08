@extends($layout ?? 'layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Create User Guide</h2>

    <form id="userGuideCreateForm" action="{{ route('user-guides.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Module --}}
        <div class="mb-3">
            <label for="module_id" class="form-label fw-semibold">Select Module <span class="text-danger">*</span></label>
            <select name="module_id" id="module_id" class="form-select" required>
                <option value="">-- Select Module --</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="module_id_error"></div>
        </div>

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">User Guide Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" maxlength="256" required>
            <div class="invalid-feedback" id="name_error"></div>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea name="description" id="description" class="form-control" maxlength="2000" rows="3"></textarea>
            <div class="invalid-feedback" id="description_error"></div>
        </div>

        {{-- Files --}}
        <div class="mb-3">
            <label for="files" class="form-label fw-semibold">Upload Files (max 20MB each)</label>
            <input type="file" name="files[]" id="files" class="form-control" multiple>
            <div id="file-list" class="mt-2"></div>
            <div class="invalid-feedback d-block" id="files_error"></div>
        </div>

        {{-- URLs --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">File URLs</label>
            <div id="url-fields">
                <input type="url" name="urls[]" class="form-control mb-2" placeholder="https://example.com">
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="add-url">Add Another URL</button>
            <div class="invalid-feedback d-block" id="urls_error"></div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Create</button>
            <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
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
