@extends('app', ['title' => $title])

@section('content')
    <div class="container grey-container m-b-2">
        {!! $md !!}
    </div>
@endsection

@push('css')
    <style>
        h2 {
            font-weight: bold;
        }
        p {
            line-height: 1.3;
        }
    </style>
@endpush