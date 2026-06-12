@extends('layouts.app')

@section('title', 'Moje wizyty - BookMe')

@section('content')
@php
    $statusy = [
        'pending'   => ['label' => 'Oczekuje',     'class' => 'bg-warning text-dark'],
        'confirmed' => ['label' => 'Potwierdzona',  'class' => 'bg-success'],
        'completed' => ['label' => 'Zakończona',    'class' => 'bg-secondary'],
        'cancelled' => ['label' => 'Anulowana',     'class' => 'bg-danger'],
    ];
@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <h1 class="h4 mb-4">Moje wizyty</h1>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="h6 text-muted mb-3">Nadchodzące</h2>
            @forelse($nadchodzace as $wizyta)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1">{{ $wizyta->service->name }}</h5>
                                <p class="mb-1">
                                    <a href="{{ route('lokal.show', $wizyta->service->business) }}" class="text-decoration-none fw-semibold">
                                        {{ $wizyta->service->business->name }}
                                    </a>
                                    <span class="text-muted small">• {{ $wizyta->employee->name }}</span>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ $wizyta->start_at->translatedFormat('l, j F Y, H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge {{ $statusy[$wizyta->status]['class'] ?? 'bg-secondary' }} mb-2">
                                    {{ $statusy[$wizyta->status]['label'] ?? $wizyta->status }}
                                </span>
                                <div class="fw-bold mb-2">{{ number_format($wizyta->total_price, 2) }} zł</div>
                                <form action="{{ route('klient.wizyty.anuluj', $wizyta) }}" method="POST"
                                      onsubmit="return confirm('Na pewno anulować tę wizytę?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle"></i> Anuluj
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-2"></i>
                        <p class="mt-2 mb-2">Nie masz zaplanowanych wizyt.</p>
                        <a href="{{ route('szukaj') }}" class="btn btn-primary btn-sm">Znajdź usługę</a>
                    </div>
                </div>
            @endforelse

            <h2 class="h6 text-muted mb-3 mt-4">Historia</h2>
            @forelse($historia as $wizyta)
                <div class="card shadow-sm mb-2">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">{{ $wizyta->service->name }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ $wizyta->service->business->name }} • {{ $wizyta->employee->name }} •
                                    {{ $wizyta->start_at->translatedFormat('j F Y, H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                <span class="badge {{ $statusy[$wizyta->status]['class'] ?? 'bg-secondary' }}">
                                    {{ $statusy[$wizyta->status]['label'] ?? $wizyta->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Brak wcześniejszych wizyt.</p>
            @endforelse

        </div>
    </div>
</div>
@endsection
