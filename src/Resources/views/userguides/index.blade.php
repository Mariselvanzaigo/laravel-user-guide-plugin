@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    <h2>User Guides</h2>

    {{-- @can('create', \ModuleUserGuide\Models\UserGuide::class) --}}
        <a href="{{ route('user-guides.create') }}" class="btn btn-primary mb-3">Add User Guide</a>
    {{-- @endcan --}}

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
        @foreach($userGuides as $guide)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $guide->module->name }}</td>
                <td>{{ $guide->name }}</td>
                <td title="{{ $guide->description }}">{{ Str::limit($guide->description,30) }}</td>
                <td>
                    @can('update', $guide)
                        <a href="{{ route('user-guides.edit', $guide) }}" class="btn btn-warning btn-sm">Edit</a>
                    @endcan

                    @can('delete', $guide)
                        <form method="POST" action="{{ route('user-guides.destroy', $guide) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guide?')">Delete</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $userGuides->links() }}
</div>
@endsection
