@extends($layout ?? 'layouts.app')

@section('title', 'User Guide')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">User Guide</h1>
    <a href="{{ route('user-guides.index') }}" class="btn btn-secondary">← Back to List</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form id="filterForm" method="GET" action="{{ route('user-guides.view') }}" class="mb-4">
            <label for="moduleSelect" class="font-weight-bold mr-2">Select Module:</label>
            <select name="module_id" id="moduleSelect" class="form-control w-auto d-inline-block"
                onchange="this.form.submit()">
                @foreach($modules as $module)
                    <option value="{{ $module->id }}"
                        {{ $selectedModule && $selectedModule->id == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
        </form>

        @if($selectedModule)
            <div id="accordion">
                @forelse($selectedModule->userGuides as $index => $guide)
                    @php
                        $collapseId = 'collapse' . $index;
                        $title = strlen($guide->title) > 30
                            ? substr($guide->title, 0, 30) . '...'
                            : $guide->title;
                    @endphp

                    <div class="card mb-2 border-0 shadow-sm">
                        <div class="card-header bg-light" data-toggle="collapse" href="#{{ $collapseId }}" style="cursor: pointer;">
                            <h6 class="mb-0 font-weight-bold text-primary" title="{{ $guide->title }}">
                                {{ $title }}
                            </h6>
                        </div>
                        <div id="{{ $collapseId }}" class="collapse {{ $index === 0 ? 'show' : '' }}" data-parent="#accordion">
                            <div class="card-body">
                                {!! $guide->content !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No user guides available for this module.</p>
                @endforelse
            </div>
        @else
            <p class="text-muted">No modules found.</p>
        @endif
    </div>
</div>
@endsection
