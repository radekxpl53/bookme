@extends('layouts.app')

@section('title', 'Ustawienia konta - BookMe')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h4 mb-4">Ustawienia konta</h1>

            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
