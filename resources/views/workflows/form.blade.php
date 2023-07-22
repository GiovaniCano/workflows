@php
    $editmode = boolval($workflow->id);
@endphp

@extends('app', ['title' => $editmode ? 'Edit: '.$workflow->name : 'Create', 'show_sidebar' => true, 'class' => 'main-center main-100vh'])

@section('content')
    @if ($editmode)
        <form id="workflow-delete-form" action="{{ route('workflow.destroy', $workflow) }}" method="POST" style="display: none">
            @method('DELETE')
            @csrf
        </form>
    @endif

    {{-- loading spinner --}}
    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>

    <form 
        action="{{ $editmode ? route('workflow.update', $workflow) : route('workflow.store') }}" 
        method="{{ $editmode ? 'PUT' : 'POST' }}" 
        id="workflow-form" 
        class="container m-b-2" 
        style="display: none"
        novalidate
    >
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
                @if ($editmode)
                    <button onclick="submitDeleteWorkflowForm()" type="button" class="btn btn-primary">{{ __('Delete') }}</button>
                    <a href="{{ route('workflow.show', ['workflow' => $workflow, 'slug' => $workflow->make_slug()]) }}" class="btn btn-primary">{{ __('Cancel') }}</a>
                @endif
                <input type="submit" value="{{ __('Save') }}" class="btn btn-primary">
            </div>
        </header>

        {{-- main sections --}}
        @foreach ($workflow->sections as $section)
            <x-workflows.section-form 
                :$section 
                class="section-main section-form" 
                :data-record_id="$section->id" 
                data-record_category="sections" 
            />
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- Created editors and added images --}}
    <script>
        var editors = {}
        var images = {}
        var deletedRecords = {
            'sections': [],
            'wysiwygs': [],
            'images': [],
        }
    </script>

    {{-- form --}}
    <script>
        /* loading */
        let loaded = false
        $('body').addClass('loading-cursor')
        $(function() {
            loaded = true
            showContent();
        })
        setTimeout(() => {
            if(!loaded) showContent()
        }, 5000);

        function showContent() {
            $('.lds-ellipsis').hide()
            $('main').removeClass('main-center').removeClass('main-100vh')
            $('#workflow-form').show()
            $('body').removeClass('loading-cursor')
        }

        /* submit delete workflow form */
        function submitDeleteWorkflowForm() {
            if (confirm("{{ __('workflows.confirm-delete-workflow') }}")) {
                window.onbeforeunload = null
                $('#workflow-delete-form').submit();
            }
        }

        /* prevent to leave the form */
        window.onbeforeunload = e => e.preventDefault()
    </script>
@endpush

@include('workflows.form-push-templates')