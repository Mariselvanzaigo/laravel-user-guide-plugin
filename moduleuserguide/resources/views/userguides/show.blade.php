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
    .container_plugin_module{
    height: auto;
    min-height: 700px;
    }
    .close_btn_fnt{
        border-radius: 50%;
        border: none;
        width: 25px;
        height: 25px;
        background-color: #bcbcbc;
    }
    .user-guide-description {
        line-height: 1.5;
        border: 1px solid #c1c1c1;
        border-radius: 4px;
        padding: 10px;
    }

    .user-guide-description table {
        border-collapse: collapse;
        width: 100%;
    }

    .user-guide-description table, 
    .user-guide-description th, 
    .user-guide-description td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .user-guide-description ul, .user-guide-description ol {
        padding-left: 20px;
    }
    .user-guide-description img{
        width: auto;
        max-width: 100%;
    }
    .preview_file_name{
        height: 35px;
        line-height: 1.2;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .container_plugin_module {
        height: auto;
        min-height: 700px;
    }
</style>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<!-- Select2 CSS for searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container mt-4 m-4 container_plugin_module">
    <div class="row">
        <!-- Back Button -->
        @if ($userRoleId == 1)
            <div><a href="{{ route($prefix . '.module-user-guide.user-guides.index') }}" class="btn btn-secondary m-2"><i class="fas fa-arrow-left me-1"></i></a></div>
        @endif
        <h1 class="mb-4">User Guides</h1>
    </div>
    <!-- Module Selection -->
    <form id="filterForm" method="GET" action="{{ route($prefix . '.module-user-guide.user-guides.display') }}" class="mb-4">
        <label for="moduleSelect">Select Module:</label>
        <select name="module_id" id="moduleSelect" class="form-select w-auto d-inline-block">
            @foreach($modules as $module)
                <option value="{{ $module->id }}" {{ $selectedModule && $selectedModule->id == $module->id ? 'selected' : '' }}>
                    {{ $module->name }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Accordion -->
    <div class="accordion-container">
        @if($selectedModule && $selectedModule->userGuides->count())
            @foreach($selectedModule->userGuides->sortByDesc('created_at') as $index => $guide)
                @php
                    $collapseId = 'guide' . $index;
                    $isFirst = $index === 0;

                    $files = [];
                    if(!empty($guide->files)) {
                        $files = is_string($guide->files) ? json_decode($guide->files, true) : $guide->files;
                    }

                    $urls = [];
                    if(!empty($guide->urls)) {
                        $urls = is_string($guide->urls) ? json_decode($guide->urls, true) : $guide->urls;
                    }
                @endphp

                <div class="accordion-item border rounded mb-2">
                    <div class="accordion-header d-flex justify-content-between align-items-center p-3 pointer {{ $isFirst ? 'active' : '' }}" 
                         data-target="#{{ $collapseId }}">
                        <span>{{ $guide->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content border-top p-3" id="{{ $collapseId }}" style="display: {{ $isFirst ? 'block' : 'none' }};">
                        <p><strong>Description:</strong> <div class="user-guide-description">{!! $guide->description !!}</div></p>

                        <!-- Files -->
                        @if(!empty($files))
                            <h6>Files:</h6>
                            <div class="row mb-3">
                                @foreach($files as $fIndex => $file)
                                    @php
                                        $filename = $file['name'] ?? 'Unknown';
                                        $path = $file['path'] ?? '';
                                        $url = asset('storage/'.$path);
                                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext,['jpg','jpeg','png','gif','webp']);
                                        $isVideo = in_array($ext,['mp4','avi','webm','mkv']);
                                        $isPdf = $ext === 'pdf';
                                        $isDoc = in_array($ext,['doc','docx']);
                                    @endphp
                                    <div class="col-md-4 mb-2">
                                        <div class="card border p-2 h-100">
                                            <p class="mb-1 preview_file_name"><strong>{{ $filename }}</strong></p>
                                            <small>Type: {{ $ext }}</small>
                                            @if($isImage)
                                                <img src="{{ $url }}" class="img-fluid rounded mt-1" style="max-height:120px;">
                                            @elseif($isVideo)
                                                <video controls style="width:100%; max-height:120px;" class="mt-1">
                                                    <source src="{{ $url }}" type="video/{{ $ext }}">
                                                </video>
                                            @elseif($isPdf)
                                                <embed src="{{ $url }}" type="application/pdf" width="100%" height="120px" class="mt-1">
                                            @elseif($isDoc)
                                                <iframe src="https://docs.google.com/gview?url={{ $url }}&embedded=true" style="width:100%; height:120px;" frameborder="0" class="mt-1"></iframe>
                                            @endif
                                            <div class="mt-2 d-flex">
                                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-primary mr-2">Open</a>
                                                {{-- <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#fileModal{{ $index }}_{{ $fIndex }}">Preview</button> --}}
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="fileModal{{ $index }}_{{ $fIndex }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered" style="max-width:900px;">
                                                <div class="modal-content" style="height:600px;">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $filename }}</h5>
                                                        <button type="button" class="btn-close close_btn_fnt" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                                                    </div>
                                                    <div class="modal-body text-center d-flex justify-content-center align-items-center" style="height:500px;">
                                                        @if($isImage)
                                                            <img src="{{ $url }}" class="img-fluid rounded" style="max-height:100%; max-width:100%; object-fit:contain;">
                                                        @elseif($isVideo)
                                                            <video controls class="rounded" style="max-height:100%; max-width:100%; object-fit:contain;">
                                                                <source src="{{ $url }}" type="video/{{ $ext }}">
                                                            </video>
                                                        @elseif($isPdf)
                                                            <embed src="{{ $url }}" type="application/pdf" width="100%" height="100%">
                                                        @elseif($isDoc)
                                                            <iframe src="https://docs.google.com/gview?url={{ $url }}&embedded=true" style="width:100%; height:100%;" frameborder="0"></iframe>
                                                        @else
                                                            <a href="{{ $url }}" target="_blank" class="btn btn-secondary">Open File</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No files uploaded.</p>
                        @endif

                        <!-- URLs -->
                        @if(!empty($urls))
                            <h6>Links:</h6>
                            <ul class="list-unstyled mt-2">
                                @foreach($urls as $url)
                                    <li><i class="fas fa-link me-1"></i> <a href="{{ $url }}" target="_blank">{{ $url }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>No user guides found for this module.</p>
        @endif
    </div>
</div>

@endsection

<style>
.pointer { cursor: pointer; }
.accordion-header { background: #f8f9fa; padding: 12px 15px; font-weight: 500; border-bottom: 1px solid #ddd; }
.accordion-header i { transition: transform 0.3s; }
.accordion-header.active i { transform: rotate(180deg); background: #cfcfcf; }
.accordion-item { box-shadow: 0 1px 4px rgba(0,0,0,0.1); border-radius: 5px; }
.accordion-content { background: #fff; border-left: 1px solid #ddd; border-right:1px solid #ddd; border-bottom:1px solid #ddd; padding:15px; margin-top:-1px; }
.file-box img, .file-box video, .file-box embed, .file-box iframe { max-height:120px; border-radius:4px; margin-top:5px; }
</style>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
var jqPlugin = jQuery.noConflict(true);

  jqPlugin(document).ready(function () {
      // Initialize Select2 safely within plugin context
      jqPlugin('#moduleSelect').select2({
          placeholder: "Select Module",
          width: '200px'
      }).on('change', function() {
          jqPlugin('#filterForm').submit();
      });
    // Accordion
    const headers = document.querySelectorAll('.accordion-header');
    headers.forEach((header, index) => {
        const targetId = header.dataset.target;
        const targetContent = document.querySelector(targetId);
        const icon = header.querySelector('i');

        if (index === 0) {
            targetContent.style.display = 'block';
            header.style.backgroundColor = '#cfcfcf';
            icon.classList.replace('fa-chevron-down','fa-chevron-up');
        } else {
            targetContent.style.display = 'none';
            icon.classList.replace('fa-chevron-up','fa-chevron-down');
        }

        header.addEventListener('click', function () {
            headers.forEach(h => {
                const c = document.querySelector(h.dataset.target);
                if (c !== targetContent) {
                    c.style.display = 'none';
                    h.style.backgroundColor = '#f8f9fa';
                    h.querySelector('i').classList.replace('fa-chevron-up','fa-chevron-down');
                }
            });

            if (targetContent.style.display === 'block') {
                targetContent.style.display = 'none';
                header.style.backgroundColor = '#f8f9fa';
                icon.classList.replace('fa-chevron-up','fa-chevron-down');
            } else {
                targetContent.style.display = 'block';
                header.style.backgroundColor = '#cfcfcf';
                icon.classList.replace('fa-chevron-down','fa-chevron-up');
            }
        });
    });
});
</script>
