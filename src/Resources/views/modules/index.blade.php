@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>Modules</h2>
    {{-- @can('create', \ModuleUserGuide\Models\Module::class) --}}
        <a href="{{ route('modules.create') }}" class="btn btn-primary mb-3">Add Module</a>
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
                    @can('update', $module)
                        <a href="{{ route('modules.edit', $module) }}" class="btn btn-warning btn-sm">Edit</a>
                    @endcan
                    @can('delete', $module)
                        <form method="POST" action="{{ route('modules.destroy', $module) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this module?')">Delete</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $modules->links() }}
</div>
@endsection
