<!-- resources/views/userguides/index.blade.php -->
@extends('larasnap::layouts.app', ['class' => 'document guide'])
@section('title','User Guide')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">User Guide</h1>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <form id="filterForm" method="GET" action="{{ route('userguides.index') }}">
            <label for="moduleSelect">Select Module:</label>
            <select name="module_id" id="moduleSelect" class="form-control w-auto d-inline-block" onchange="this.form.submit()">
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $selectedModule && $selectedModule->id == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="col-md-12">
        <div id="accordion">
            @if($selectedModule && $selectedModule->userGuides->count())
                @foreach($selectedModule->userGuides as $index => $guide)
                    @php
                        $collapseId = 'collapse' . ($index + 1);
                        $title = strlen($guide->title) > 30 ? substr($guide->title,0,30).'...' : $guide->title;
                    @endphp
                    <div class="card mb-2">
                        <div class="card-header collapsed" data-toggle="collapse" href="#{{ $collapseId }}">
                            <a class="card-title" title="{{ $guide->title }}">{{ $title }}</a>
                        </div>
                        <div id="{{ $collapseId }}" class="card-body collapse {{ $index == 0 ? 'show' : '' }}" data-parent="#accordion">
                            {!! $guide->content !!}
                        </div>
                    </div>
                @endforeach
            @else
                <p>No User Guides found for this module.</p>
            @endif
        </div>
    </div>
</div>
@endsection
