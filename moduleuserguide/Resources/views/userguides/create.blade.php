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
                <option value="">Select Module</option>
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
            <label for="fileInput" class="form-label fw-semibold">Upload Files (max 20MB each)</label>
            <input type="file" id="fileInput" class="form-control" multiple>
            <div id="file-list" class="mt-2"></div>
            <div class="invalid-feedback d-block" id="files_error"></div>
        </div>

        {{-- URLs --}}
        <div class="mb-3" id="url-container">
            <label class="form-label fw-semibold">File URLs</label>
            <div id="url-fields"></div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="add-url">Add URL</button>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Create</button>
            <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
@push('styles')
<link href="{{ asset('plugin-assets/css/toastr.min.css') }}" rel="stylesheet">
<link href="{{ asset('plugin-assets/css/userguide.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugin-assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('plugin-assets/js/userguide.js') }}"></script>
@endpush