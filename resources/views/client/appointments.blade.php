@extends('layouts.app')

@section('title', 'Moje wizyty - BookMe')

@section('content')
@php
    $statuses = [
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
            @forelse($upcoming as $appt)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1">{{ $appt->service->name }}</h5>
                                <p class="mb-1">
                                    <a href="{{ route('lokal.show', $appt->service->business) }}" class="text-decoration-none fw-semibold">
                                        {{ $appt->service->business->name }}
                                    </a>
                                    <span class="text-muted small">• {{ $appt->employee->name }}</span>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ $appt->start_at->format('d.m.Y, H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge {{ $statuses[$appt->status]['class'] ?? 'bg-secondary' }} mb-2">
                                    {{ $statuses[$appt->status]['label'] ?? $appt->status }}
                                </span>
                                <div class="fw-bold mb-2">{{ number_format($appt->total_price, 2) }} zł</div>
                                <div class="d-flex gap-2 justify-content-md-end">
                                    <a href="{{ route('klient.wizyty.zmien', $appt) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-calendar-event"></i> Zmień termin
                                    </a>
                                    <form action="{{ route('klient.wizyty.anuluj', $appt) }}" method="POST"
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
            @forelse($history as $appt)
                <div class="card shadow-sm mb-2">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">{{ $appt->service->name }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ $appt->service->business->name }} • {{ $appt->employee->name }} •
                                    {{ $appt->start_at->format('d.m.Y, H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                <span class="badge {{ $statuses[$appt->status]['class'] ?? 'bg-secondary' }}">
                                    {{ $statuses[$appt->status]['label'] ?? $appt->status }}
                                </span>

                                @if($appt->status === 'completed')
                                    <div class="mt-2">
                                        <a href="{{ route('klient.opinia.create', $appt) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-star"></i>
                                            {{ $reviewedEmployees->contains($appt->employee_id) ? 'Edytuj opinię' : 'Oceń specjalistę' }}
                                        </a>
                                    </div>
                                @endif
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
