@php
    $editmode = boolval($workflow->id);
@endphp

@extends('app', ['title' => $editmode ? 'Edit: '.$workflow->name : 'Create', 'show_sidebar' => true])

@section('content')
    <form id="workflow-delete-form" action="{{ route('workflow.destroy', $workflow) }}" method="POST" style="display: none">
        @method('DELETE')
        @csrf
    </form>

    <form method="POST" class="container m-b-2" id="workflow-form">
        <header class="workflow-header grey-container">
            <x-form-control 
                class="m-0 h1" 
                maxlength="25" 
                type="text" 
                name="workflow_name" 
                :value="$editmode ? $workflow->name : ''"
                :placeholder="__('workflows.workflow-placeholder')"
                required
            />

            <div>
                <button onclick="submitDeleteWorkflowForm()" type="button" class="btn btn-edition">{{ __('Delete') }}</button>
                <a href="{{ route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()]) }}" class="btn btn-primary">{{ __('Cancel') }}</a>
                <input type="submit" value="{{ __('Save') }}" class="btn btn-edition">
            </div>
        </header>

        {{-- main sections --}}
        @foreach ($workflow->sections as $section)
            <x-workflows.section-form :$section class="section-main section-form js-action-target" />
        @endforeach

        <input type="submit" value="{{ __('Save') }}" class="btn btn-primary align-center m-t-2 w-50 m-b-2">

        @stack('mini-sections')
    </form>
@endsection

@push('js')
    {{-- CKEditor --}}
    <script src="/js/ckeditor/ckeditor.js"></script>
    @if (app()->getLocale() !== 'en')
        <script src="/js/ckeditor/translations/{{ app()->getLocale() }}.js"></script>
    @endif

    {{-- jQuery UI --}}
    <script src="/js/jquery/jquery-ui.min.js"></script>

    {{-- form --}}
    <script>
        /* submit delete workflow form */
        function submitDeleteWorkflowForm() {
            if (confirm("{{ __('workflows.confirm-delete-workflow') }}")) {
                window.onbeforeunload = null
                $('#workflow-delete-form').submit();
            }
        }

        /* prevent to leave the form */
        // window.onbeforeunload = e => e.preventDefault()
    </script>
@endpush

@include('workflows.form-push-templates')