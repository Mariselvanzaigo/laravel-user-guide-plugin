@extends($layout ?? 'layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Create User Guide</h2>

    <form id="userGuideCreateForm" action="{{ route('user-guides.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Module --}}
        <div class="mb-3">
            <label for="module_id" class="form-label fw-semibold">
                Select Module <span class="text-danger">*</span>
            </label>
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
            <label for="name" class="form-label fw-semibold">
                User Guide Name <span class="text-danger">*</span>
            </label>
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
            <label for="files" class="form-label fw-semibold">
                Upload Files (max 20MB each)
            </label>
            <input type="file" id="fileInput" class="form-control" multiple>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('userGuideCreateForm');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('file-list');
    const allowedTypes = [
        'image/jpeg','image/png','image/jpg','application/pdf',
        'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','video/mp4'
    ];
    const filesArray = [];

    // --- Field validation ---
    const validateField = (input) => {
        const errorEl = document.getElementById(`${input.id}_error`) || document.getElementById('urls_error');
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
            errorEl.textContent = 'Enter a valid URL (https://...)';
            input.classList.add('is-invalid');
            return false;
        }
        return true;
    };

    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', () => validateField(input));
        input.addEventListener('change', () => validateField(input));
    });

    // --- File upload and preview ---
    fileInput.addEventListener('change', (e) => {
        const newFiles = Array.from(e.target.files);
        document.getElementById('files_error').textContent = '';

        newFiles.forEach(file => {
            if (!allowedTypes.includes(file.type)) {
                document.getElementById('files_error').textContent = 'Invalid file type detected.';
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                document.getElementById('files_error').textContent = 'File exceeds 20MB limit.';
                return;
            }
            if (!filesArray.some(f => f.name === file.name)) {
                filesArray.push(file);
            }
        });

        renderFileList();
        fileInput.value = '';
    });

    function renderFileList() {
        fileList.innerHTML = '';
        filesArray.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center justify-content-between border rounded p-2 mb-1';
            div.innerHTML = `<span>${file.name}</span>
                <button type="button" class="btn btn-sm btn-outline-danger">Delete</button>`;
            div.querySelector('button').addEventListener('click', () => {
                filesArray.splice(index, 1);
                renderFileList();
            });
            fileList.appendChild(div);
        });
    }

    // --- Add URL inputs ---
    document.getElementById('add-url').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'url';
        input.name = 'urls[]';
        input.className = 'form-control mb-2';
        input.placeholder = 'https://example.com';
        input.addEventListener('input', () => validateField(input));
        document.getElementById('url-fields').appendChild(input);
    });

    // --- Submit ---
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let valid = true;

        form.querySelectorAll('input, select, textarea').forEach(input => {
            if (!validateField(input)) valid = false;
        });

        if (filesArray.length > 0) {
            for (const file of filesArray) {
                if (!allowedTypes.includes(file.type) || file.size > 20 * 1024 * 1024) valid = false;
            }
        }

        if (!valid) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        const formData = new FormData(form);
        filesArray.forEach(f => formData.append('files[]', f));

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value}
        })
        .then(res => res.json())
        .then(data => {
            if (data.errors) {
                // Show backend errors inline
                Object.keys(data.errors).forEach(key => {
                    const el = document.getElementById(`${key}_error`);
                    if (el) {
                        el.textContent = data.errors[key][0];
                        const input = document.querySelector(`[name="${key}"]`);
                        input?.classList.add('is-invalid');
                    }
                });
            } else {
                alert('User Guide created successfully!');
                window.location.href = "{{ route('user-guides.index') }}";
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    });
});
</script>
@endsection
