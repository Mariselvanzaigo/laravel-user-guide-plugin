@extends($layout ?? 'layouts.app')

@section('content')
<div class="container py-4">
    <h2>Edit User Guide</h2>

    <form id="userGuideEditForm" action="{{ route('user-guides.update', $userGuide) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        {{-- Module --}}
        <div class="mb-3">
            <label for="module_id" class="form-label fw-semibold">Select Module <span class="text-danger">*</span></label>
            <select name="module_id" id="module_id" class="form-select" required>
                <option value="">Select Module</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $userGuide->module_id == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="module_id_error"></div>
        </div>

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">User Guide Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" maxlength="256" value="{{ old('name', $userGuide->name) }}" required>
            <div class="invalid-feedback" id="name_error"></div>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea name="description" id="description" class="form-control" maxlength="2000" rows="3">{{ old('description', $userGuide->description) }}</textarea>
            <div class="invalid-feedback" id="description_error"></div>
        </div>

        {{-- Files --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Uploaded Files</label>

            <div id="existing-files" class="row g-2 mb-2">
                @if($userGuide->files)
                    @foreach($userGuide->files ?? [] as $file)
                        <div class="col-md-6">
                            <div class="border rounded p-2 d-flex justify-content-between align-items-center existing-file-box" 
                                data-existing-file="{{ $file['path'] }}" title="{{ $file['name'] }}">
                                <a href="{{ Storage::url($file['path']) }}" target="_blank" class="text-truncate" style="max-width: 75%">
                                    {{ \Illuminate\Support\Str::limit($file['name'], 30) }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-existing-file">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <input type="file" name="files[]" id="fileInput" class="form-control" multiple>
            <div id="file-list" class="mt-2 row g-2"></div>
            <div class="invalid-feedback d-block" id="files_error"></div>
        </div>

        {{-- URLs --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">File URLs</label>
            <div id="url-fields">
                @if($userGuide->urls)
                    @foreach($userGuide->urls as $url)
                        <input type="url" name="urls[]" class="form-control mb-2" value="{{ $url }}">
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="add-url">Add URL</button>
            <div class="invalid-feedback d-block" id="urls_error"></div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Update</button>
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
