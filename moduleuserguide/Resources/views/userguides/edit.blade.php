@extends($layout ?? 'layouts.app')

@section('content')
<style>
    /* userguide.css */
#file-list, #existing-files {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 0.75rem;
  list-style: none;
  padding: 0;
  margin: 0;
}

.file-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .5rem;
  padding: .5rem .75rem;
  border: 1px solid #e6eef6;
  border-radius: 8px;
  background: #ffffff;
  box-shadow: 0 1px 2px rgba(16,24,40,0.03);
  min-height: 48px;
  overflow: hidden;
}

.file-meta {
  display: flex;
  align-items: center;
  gap: .75rem;
  min-width: 0;
}

.file-icon {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #f3f7fb;
  color: #2b6cb0;
  font-weight: 600;
  flex-shrink: 0;
}

.file-name {
  display: inline-block;
  max-width: calc(100% - 20px);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: .95rem;
}

.file-actions {
  display:flex;
  align-items:center;
  gap:.5rem;
  flex-shrink: 0;
}

.file-size {
  color: #6b7280;
  font-size: .80rem;
}
.url-row {
  display: flex;
  gap: 0.5rem; /* spacing between input and button/error */
  flex-wrap: wrap; /* allow error to go below */
  align-items: flex-start;
}

.url-row input.form-control {
  flex: 0 0 45; /* always 50% width */
  max-width: 45%;
}

.url-row .remove-url {
  flex: 0 0 3%; /* button width, adjust if needed */
  max-width: 3%;
  padding: 7px;
}

.url-row .invalid-feedback {
  flex: 0 0 50%; /* error text takes remaining 40% */
  max-width: 50%;
  display: block; /* JS can toggle visibility */
  margin-top: 0.25rem; 
  padding-top: 4px;
}
/* small screen: single column */
@media (max-width: 576px) {
  #file-list, #existing-files {
    grid-template-columns: repeat(1, 1fr);
  }
}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
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
                        <div class="d-flex gap-2 mb-2 url-row">
                            <input type="url" name="urls[]" class="form-control" placeholder="https://example.com" value="{{ $url }}">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-url">
                                <i class="fa fa-trash"></i>
                            </button>
                            <div class="invalid-feedback"></div>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="add-url">Add URL</button>
        </div>


        <div class="mt-3">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="{{ url('plugin-assets/css/toastr.min.css') }}" rel="stylesheet">
{{-- <link href="{{ url('plugin-assets/css/userguide.css') }}" rel="stylesheet"> --}}
@endpush

@push('scripts')
<script src="{{ url('plugin-assets/js/toastr.min.js') }}"></script>
<script src="{{ url('plugin-assets/js/userguide.js') }}"></script>
@endpush
