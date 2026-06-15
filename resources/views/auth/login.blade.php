@extends('layouts.app')

@section('title', 'Logowanie - BookMe')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zaloguj się</h5>
                </div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label">E-mail lub Nazwa użytkownika</label>
                            <input type="text" class="form-control" id="login" name="login"
                                   value="{{ old('login') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Hasło</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                            <label class="form-check-label" for="remember_me">Zapamiętaj mnie</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Zaloguj się</button>
                        </div>

                        <p class="text-center mt-3 mb-0">
                            Nie masz konta?
                            <a href="{{ route('register') }}">Załóż konto</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
