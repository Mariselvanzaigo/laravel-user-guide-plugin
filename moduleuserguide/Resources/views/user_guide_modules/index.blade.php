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
    .table td, .table th {
        padding: 0.50rem !important;
    }
    .container_plugin_module {
        height: auto;
        min-height: 700px;
    }
</style>

<div class="container container_plugin_module m-4">
    <h2>Modules</h2>

    {{-- @can('create', \ModuleUserGuide\Models\Module::class) --}}
    <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.create') }}" 
       class="btn btn-primary mb-3">Add Module</a>
    <a href="{{ route($prefix . '.module-user-guide.user-guides.index') }}" 
       class="btn btn-primary mb-3">User Guide</a>
    {{-- @endcan --}}

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($modules as $module)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $module->name }}</td>
                <td>
                    {{-- @can('update', $module) --}}
                    <a href="{{ route($prefix . '.module-user-guide.user_guide_modules.edit', $module) }}" 
                       class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                    {{-- @endcan --}}
                    
                    {{-- @can('delete', $module) --}}
                    <form method="POST" 
                          action="{{ route($prefix . '.module-user-guide.user_guide_modules.destroy', $module) }}" 
                          style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" 
                                onclick="return confirm('Are you sure you want to delete this module?')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                    {{-- @endcan --}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $modules->links() }}
</div>
@endsection
