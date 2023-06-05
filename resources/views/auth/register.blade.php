@extends('app', ['title' => __('Register'), 'hide_brand' => true, 'class' => 'main-center main-100vh'])

@section('meta')
    <x-meta title="Register" desckey="register" route="register"/>
@endsection

@section('content')
    <x-brand :big="true" href="{{route('landing-page')}}" class="brand-auth" />

    <form class="form glass auth-form m-t-2" action="{{route('register')}}" method="POST">
        <h1>{{__('Register')}}</h1>

        @csrf

        {{-- email --}}
        <x-form-control name="email" type="email" placeholder="Email" required/>
        
        {{-- name --}}
        <x-form-control name="name" type="text" placeholder="Name" required/>
        
        {{-- password --}}
        <x-form-control name="password" type="password" placeholder="Password" minlength="8" required/>
        <x-form-control name="password_confirmation" type="password" placeholder="Confirm password" required/>
        
        {{-- terms --}}
        <x-form-control type="checkbox" name="terms" required>
            {!! __('I have read and agree to the :terms and :privacy.', [
                'terms' => '<a href="'.route('terms').'" target="_blank" tabindex="-1">' . __('Terms and Conditions') . '</a>',
                'privacy' => '<a href="'.route('privacy').'" target="_blank" tabindex="-1">' . __('Privacy Policy') . '</a>'
            ]) !!}
        </x-form-control>
    
        {{-- submit --}}
        <input class="btn btn-primary m-t-2" type="submit" value="{{__('Register')}}">
    </form>

    <div class="grey-container links m-b-3">
        <a class="btn btn-primary d-b" href="{{route('login')}}">{{__('Login')}}</a>
        <a class="btn btn-primary d-b m-t-1" href="#">{{__('Reset Password')}}</a>
    </div>
@endsection