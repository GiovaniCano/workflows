@extends('app', ['title' => __('Log In'), 'hide_brand' => true, 'class' => 'main-center main-100vh'])

@section('meta')
    <x-meta title="Log In" desckey="login" route="login"/>
@endsection

@section('content')
    <x-brand :big="true" href="{{ route('landing-page') }}" class="brand-auth" />

    <form class="form glass auth-form m-t-2" action="{{ route('login') }}" method="POST">
        <h1>{{ __('Log In') }}</h1>

        @csrf

        <x-form-control name="email" type="email" placeholder="Email" required />
        <x-form-control name="password" type="password" placeholder="Password" required />

        <x-form-control type="checkbox" name="remember">
            {{ __('Keep me logged in') }}
        </x-form-control>

        <input class="btn btn-primary m-t-2" type="submit" value="{{ __('Log In') }}">
    </form>

    <div class="grey-container links m-b-3">
        <a class="btn btn-primary d-b" href="{{ route('register') }}">{{ __('Register') }}</a>
        {{-- <a class="btn btn-primary d-b m-t-1" href="#">{{ __('Reset Password') }}</a> --}}
    </div>
@endsection
