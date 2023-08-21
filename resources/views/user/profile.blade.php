@extends('app', ['title' => 'Profile', 'show_sidebar' => true])

@section('content')
    <form class="form form-profile m-t-2" method="POST" action="{{ route('user-profile-information.update') }}">
        <h2 class="text-center m-0">{{ __('Profile') }}</h2>

        @csrf
        @method('PUT')

        <x-form-control name="email" type="email" placeholder="Email" value="{{ auth()->user()->email }}" bag="updateProfileInformation" required/>
        <x-form-control name="name" type="text" placeholder="Name" value="{{ auth()->user()->name }}" bag="updateProfileInformation" required/>

        <input class="btn btn-primary m-t-2" type="submit" value="{{__('Save')}}">
    </form>

    <form class="form form-profile m-t-2" method="POST" action="{{ route('user-password.update') }}">
        <h2 class="text-center m-0">{{ __('Password') }}</h2>

        @csrf
        @method('PUT')
        
        <x-form-control name="current_password" type="password" placeholder="Current password" bag="updatePassword" required/>
        <x-form-control name="password" type="password" placeholder="New password" minlength="8" bag="updatePassword" required/>
        <x-form-control name="password_confirmation" type="password" placeholder="Confirm password" bag="updatePassword" required/>

        <input class="btn btn-primary m-t-2" type="submit" value="{{__('Save')}}">
    </form>

    <form class="form form-profile m-t-2 m-b-2" method="POST" action="{{ route('user.destroy') }}">
        <h2 class="text-center m-0">{{ __('Delete account') }}</h2>

        @csrf
        @method('DELETE')

        {{-- <p class="text-center">{{ __('This action cannot be undone') }}</p> --}}

        <input class="btn btn-danger m-t-1" type="submit" value="{{__('Delete')}}">
    </form>
@endsection