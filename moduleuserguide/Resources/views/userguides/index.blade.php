@extends($layout ?? 'layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
    <h2>User Guides</h2>

    <a href="{{ route('user-guides.create') }}" class="btn btn-primary mb-3">Add User Guide</a>
    <a href="{{ route('user-guides.view') }}" class="btn btn-primary mb-3">View User Guide</a>

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
                <td title="{{ $guide->description }}">{{ Str::limit($guide->description, 30) }}</td>
                <td>
                    <a href="{{ route('user-guides.edit', $guide) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>

                    <form method="POST" action="{{ route('user-guides.destroy', $guide) }}" style="display:inline">
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

@push('styles')
<link href="{{ url('plugin-assets/css/toastr.min.css') }}" rel="stylesheet">
<link href="{{ url('plugin-assets/css/userguide.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ url('plugin-assets/js/toastr.min.js') }}"></script>
<script src="{{ url('plugin-assets/js/userguide.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const msg = sessionStorage.getItem('userGuideSuccess');
    if(msg){
        toastr.success(msg);
        sessionStorage.removeItem('userGuideSuccess');
    }
});
</script>
@endpush
