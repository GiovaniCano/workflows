@extends('app', ['title' => $workflow->name, 'show_sidebar' => true])

@section('content')
    <div class="container m-b-2">
        <header class="workflow-header grey-container">
            <h1>{{ $workflow->name }}</h1>
            <a class="btn btn-primary" href="{{route('workflow.edit', ['workflow' => $workflow, 'slug' => $workflow->make_slug()])}}">{{ __('Edit') }}</a>
        </header>

        {{-- main sections --}}
        @foreach ($workflow->sections as $section)
            <x-workflows.section :$section class="section-main" />
        @endforeach
    </div>
@endsection

@push('templates')
    {{-- modal --}}
    <script id="modal-template" type="text/template">
        <x-modal/>
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="/prism/prism.css">
@endpush
@push('js')
    <script src="/prism/prism.js"></script>
@endpush