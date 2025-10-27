@php
if (view()->exists('larasnap::layouts.app')) {
    $layoutToUse = 'larasnap::layouts.app';
} else {
    $layoutToUse = $layout ?? 'layouts.app';
}

// Dynamic prefix based on first URL segment
$prefix = request()->segment(1) ?? 'default';
@endphp

@extends($layoutToUse)

@section('content')
<style>
    /* userguide.css */
#file-list, #existing-files {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
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
.select2 .selection .select2-selection{
    height: 38px;
    padding: 5px;
}
.select2 .selection .select2-selection .select2-selection__arrow{
  margin-top: 5px;
}
.select2 .selection .is-invalid{
  border-color: #e74a3b;
  padding-right: 10px !important;
  background-image: none !important;
  background-repeat: no-repeat;
  background-position: center right calc(0.375em + 0.1875rem);
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
#fileInput{
  padding: 8px;
  height: 45px;
}
.url-row {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  flex-wrap: nowrap;
}

.url-input-wrapper,
.url-error-wrapper {
  flex: 1 1 50%;
}

.url-error-wrapper .invalid-feedback {
  font-size: 0.875rem;
  color: #dc3545;
  margin-top: -7px;
}
/* small screen: single column */
@media (max-width: 576px) {
  #file-list, #existing-files {
    grid-template-columns: repeat(1, 1fr);
  }
}
.container_plugin_module{
  height: auto;
  min-height: 700px;
}
.ck-editor__editable_inline {
    min-height: 300px; /* Set your preferred height */
}
</style>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<!-- Select2 CSS for searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container py-4 container_plugin_module m-4">
  <div class="row">
    <!-- Back Button -->
    <div style="margin-left: 7px;">
        <a href="{{ route($prefix . '.module-user-guide.user-guides.index') }}" class="btn btn-secondary m-1">
            <i class="fas fa-arrow-left me-1"></i>
        </a>
    </div>
    <h2 class="mb-4">Create User Guide</h2>
  </div>

  <form id="userGuideCreateForm" action="{{ route($prefix . '.module-user-guide.user-guides.store') }}" method="POST" enctype="multipart/form-data" novalidate>
      @csrf

      {{-- Module --}}
      <div class="mb-3">
          <label for="module_id" class="form-label fw-semibold">Select Module <span class="text-danger">*</span></label><br>
          <select name="module_id" id="module_id" class="form-select w-auto d-inline-block" required>
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
          <input type="text" name="name" id="name" class="form-control" maxlength="256" placeholder="Enter User Guide Name" required>
          <div class="invalid-feedback" id="name_error"></div>
      </div>

      {{-- Description --}}
      <div class="mb-3">
          <label for="description" class="form-label fw-semibold">Description</label>
          <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
            maxlength="20000" rows="6" placeholder="Enter Description"></textarea>
          <div class="invalid-feedback" id="description_error"></div>
      </div>

      {{-- Files --}}
      <div class="mb-3">
          <label for="fileInput" class="form-label fw-semibold">Upload Files (max 20MB each)</label>
          {{-- <input type="file" id="fileInput" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.mp4" multiple> --}}
          <input type="file" id="fileInput" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.mp4" multiple>
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
          <a href="{{ route($prefix . '.module-user-guide.user-guides.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
  </form>
</div>
@endsection

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  
var jqPlugin = jQuery.noConflict(true);
  jqPlugin(document).ready(function () {alert(1);
      // Initialize Select2 safely within plugin context
      jqPlugin('#module_id').select2({
          placeholder: "Select Module",
          width: '200px'
      }).on('change', function() {
        //  if (window.UserGuidePlugin && typeof UserGuidePlugin.validateField === 'function') {
            validateField(this);
        // }
      });
    });
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
  // Dynamic CKEditor upload URL
  window.ckEditorUploadUrl = "{{ route($prefix . '.module-user-guide.user-guides.upload-image') }}?_token={{ csrf_token() }}";
</script>
<link href="{{ asset('vendor/moduleuserguide/css/toastr.min.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/moduleuserguide/js/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/moduleuserguide/js/userguide.js') }}"></script>