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
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container container_plugin_module m-4">
    <h2>User Guides</h2>

    <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.create') }}" 
       class="btn btn-primary mb-3">Add Module</a>
    <a href="{{ route($prefix . '.module-user-guide.user-guides.create') }}" class="btn btn-primary mb-3">Add User Guide</a>
    <a href="{{ route($prefix . '.module-user-guide.user-guides.show') }}" class="btn btn-primary mb-3">View User Guide</a>

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
        @foreach($userGuides as $index => $guide)
            <tr>
                {{-- Serial number calculation based on pagination --}}
                <td>{{ $userGuides->firstItem() + $index }}</td>
                <td>{{ $guide->module->name }}</td>
                <td>{{ $guide->name }}</td>
                <td title="{{ html_entity_decode(strip_tags($guide->description)) }}">
                    {{ Str::limit(html_entity_decode(strip_tags($guide->description)), 30) }}
                </td>
                <td>
                    <a href="{{ route($prefix . '.module-user-guide.user-guides.edit', $guide) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>

                    <form method="POST" action="{{ route($prefix . '.module-user-guide.user-guides.destroy', $guide) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guide?')"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Laravel pagination links --}}
    <div class="d-flex justify-content-center">
        {{ $userGuides->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

<link href="{{ url('moduleuserguide/css/toastr.min.css') }}" rel="stylesheet">
<link href="{{ url('moduleuserguide/css/userguide.css') }}" rel="stylesheet">
<script src="{{ url('moduleuserguide/js/toastr.min.js') }}"></script>
<script src="{{ url('moduleuserguide/js/userguide.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const msg = sessionStorage.getItem('userGuideSuccess');
    if(msg){
        toastr.success(msg);
        sessionStorage.removeItem('userGuideSuccess');
    }
});
</script>
