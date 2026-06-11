@extends('layouts.app')

@section('title', 'Panel Administratora - Podsumowanie')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Panel Administratora</h2>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Oczekujące lokale</h5>
                    <p class="display-4">{{ $pendingBusinesses }}</p>
                    <a href="{{ route('admin.businesses', ['status' => 'pending']) }}" class="text-white text-decoration-none stretched-link">Przejdź do weryfikacji <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Zatwierdzone lokale</h5>
                    <p class="display-4">{{ $totalBusinesses - $pendingBusinesses }}</p>
                    <a href="{{ route('admin.businesses', ['status' => 'approved']) }}" class="text-white text-decoration-none stretched-link">Zobacz lokale <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Wszyscy użytkownicy</h5>
                    <p class="display-4">{{ $totalUsers }}</p>
                    <a href="{{ route('admin.users') }}" class="text-white text-decoration-none stretched-link">Zarządzaj <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Właściciele biznesów</h5>
                    <p class="display-4">{{ $totalOwners }}</p>
                    <a href="{{ route('admin.users', ['role' => 'owners']) }}" class="text-white text-decoration-none stretched-link">Zobacz <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
