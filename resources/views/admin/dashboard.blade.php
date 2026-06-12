@extends('layouts.app')

@section('title', 'Panel Administratora - Statystyki i Podsumowanie')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Panel Główny</h2>
            <p class="text-muted mb-0">Zarządzanie całą platformą BookMe</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border p-2">Status Systemu: <span class="text-success fw-bold">Online</span></span>
        </div>
    </div>

    <h5 class="fw-bold mb-4 text-muted border-bottom pb-2">Kluczowe wskaźniki (Platforma)</h5>
    <div class="row g-4 mb-5">
        <!-- Rezerwacje -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bi bi-calendar-check display-1 text-primary"></i>
                </div>
                <div class="card-body position-relative z-1">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Całkowita liczba wizyt</h6>
                    <h2 class="display-5 fw-bold mb-3">{{ $totalAppointments }}</h2>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $completionRate }}%"></div>
                        </div>
                        <span class="ms-3 text-muted small fw-bold">{{ $completionRate }}% Zakończonych</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Użytkownicy -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bi bi-people display-1 text-success"></i>
                </div>
                <div class="card-body position-relative z-1">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Zarejestrowani Użytkownicy</h6>
                    <h2 class="display-5 fw-bold mb-3">{{ $totalUsers }}</h2>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i>Klienci: {{ $totalUsers - $totalOwners }}</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-briefcase me-1"></i>Biznesy: {{ $totalOwners }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opinie -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bi bi-star-fill display-1 text-warning"></i>
                </div>
                <div class="card-body position-relative z-1">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Opinie w systemie</h6>
                    <h2 class="display-5 fw-bold mb-3">{{ $totalReviews }}</h2>
                    <p class="text-muted small mb-0"><i class="bi bi-chat-square-quote me-1"></i>Oceny zebrane z całej platformy</p>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-4 text-muted border-bottom pb-2">Zarządzanie Lokalami</h5>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card bg-warning text-dark border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-bold mb-1">Oczekujące lokale</h5>
                        <p class="mb-0">Wymagają Twojej weryfikacji</p>
                    </div>
                    <div class="text-center">
                        <h1 class="display-4 fw-bold mb-0">{{ $pendingBusinesses }}</h1>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.businesses', ['status' => 'pending']) }}" class="btn btn-dark w-100 fw-bold">Weryfikuj zgłoszenia <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-bold mb-1">Zatwierdzone lokale</h5>
                        <p class="text-muted mb-0">Aktywne biznesy na platformie</p>
                    </div>
                    <div class="text-center">
                        <h1 class="display-4 fw-bold text-success mb-0">{{ $totalBusinesses - $pendingBusinesses }}</h1>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.businesses', ['status' => 'approved']) }}" class="btn btn-outline-success w-100 fw-bold">Zarządzaj lokalami <i class="bi bi-list ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
