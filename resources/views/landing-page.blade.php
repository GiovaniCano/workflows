@extends('app', ['hide_brand' => true])

@section('meta')
    <x-meta desckey="landing" route="landing-page"/>
@endsection

@section('content')

    <x-brand class="brand-landing" :heading="true" :big="true" href="" />
    <div id="hero">

    </div>

    {{-- <div class="container m-t-2" style="background-color: red">
        sadasdasd
    </div> --}}
@endsection