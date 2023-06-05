@extends('app', ['show_sidebar' => true, 'class' => 'main-center main-100vh'])

@section('content')
    <div class="container m-b-2">
        <a class="align-center btn btn-secondary" href="{{ route('workflow.create') }}">{{ __('workflows.create-first') }}</a>
    </div>
@endsection