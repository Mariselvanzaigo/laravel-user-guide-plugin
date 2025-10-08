@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Module</h2>
    <form action="{{ route('modules.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Module Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required maxlength="256">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button class="btn btn-success">Create</button>
        <a href="{{ route('modules.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
