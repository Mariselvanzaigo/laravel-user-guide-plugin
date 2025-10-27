@php
if (view()->exists('larasnap::layouts.app')) {
    $layoutToUse = 'larasnap::layouts.app';
} else {
    $layoutToUse = $layout ?? 'layouts.app';
}
$prefix = request()->segment(1) ?? 'default';
@endphp

@extends($layoutToUse)

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('vendor/moduleuserguide/css/toastr.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/moduleuserguide/css/userguide.css') }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="{{ asset('vendor/moduleuserguide/js/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/moduleuserguide/js/userguide.js') }}"></script>

<style>
.container_plugin_module {
    min-height: 700px;
}
.select2 .selection .select2-selection{
    height: 38px;
    padding: 5px;
}
.select2-container {
    width: 200px !important;
}

.select2 .selection .select2-selection  {
    width: 200px !important;
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
</style>

<div class="container container_plugin_module m-4">
    <h2>User Guides</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- Left: Search + Module Filter --}}
        <form method="GET" action="{{ route($prefix . '.module-user-guide.user-guides.index') }}" class="d-flex" id="filterForm">
            <input type="text" name="search" class="form-control me-2" placeholder="Search user guides..." value="{{ $searchQuery }}">
            <button type="submit" class="btn btn-secondary ml-2 mr-2">Search</button>
            <select name="module_id" class="form-control me-2 select2" style="width: 220px;" data-placeholder="Select Module" id="userGuideFilter">
                <option value="">All Modules</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $selectedModuleId == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route($prefix . '.module-user-guide.user-guides.index') }}" class="btn btn-warning ml-2">Reset</a>
        </form>

        {{-- Right: Action Buttons --}}
        <div>
            <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.create') }}" class="btn btn-primary">Add Module</a>
            <a href="{{ route($prefix . '.module-user-guide.user-guides.create') }}" class="btn btn-primary">Add User Guide</a>
            <a href="{{ route($prefix . '.module-user-guide.user-guides.display') }}" class="btn btn-primary">View User Guide</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Module</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if($userGuides->isEmpty())
                <tr>
                    <td colspan="5" class="text-center alert alert-info">No user guides found.</td>
                </tr>
            @else
                @foreach($userGuides as $index => $guide)
                    <tr>
                        <td>{{ $userGuides->firstItem() + $index }}</td>
                        <td>{{ $guide->module->name }}</td>
                        <td>{{ $guide->name }}</td>
                        <td title="{{ html_entity_decode(strip_tags($guide->description)) }}">
                            {{ Str::limit(html_entity_decode(strip_tags($guide->description)), 50) }}
                        </td>
                        <td>
                            <a href="{{ route($prefix . '.module-user-guide.user-guides.edit', $guide) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <form method="POST" action="{{ route($prefix . '.module-user-guide.user-guides.destroy', $guide) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- Pagination --}}
        {{ $userGuides->appends(['search' => $searchQuery, 'module_id' => $selectedModuleId])->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Select2 first
    $('#userGuideFilter').select2({
        width: 'resolve',  // respects CSS width
        minimumResultsForSearch: 5, // hides search if few options
        // allowClear: true,
        placeholder: $('#userGuideFilter').data('placeholder'),
        dropdownAutoWidth: false
    });

    // Automatically submit form on module dropdown change
    $('#userGuideFilter').on('select2:select', function () {
        $('#filterForm').submit();
    });
});
// Toastr session success
document.addEventListener('DOMContentLoaded', () => {
    const msg = sessionStorage.getItem('userGuideSuccess');
    if(msg){
        toastr.success(msg);
        sessionStorage.removeItem('userGuideSuccess');
    }
});
</script>
@endsection
