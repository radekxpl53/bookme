@extends('layouts.app')

@section('title', 'Rezerwacja przyjęta - BookMe')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                    <h1 class="h4 mt-3">Rezerwacja przyjęta!</h1>
                    <p class="text-muted">Twoja wizyta została zapisana. Status: oczekuje na potwierdzenie lokalu.</p>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Lokal</span>
                        <strong>{{ $appointment->service->business->name }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Usługa</span>
                        <strong>{{ $appointment->service->name }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Specjalista</span>
                        <strong>{{ $appointment->employee->name }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Termin</span>
                        <strong>{{ $appointment->start_at->translatedFormat('l, j F Y, H:i') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Cena</span>
                        <strong class="text-primary">{{ number_format($appointment->total_price, 2) }} zł</strong>
                    </li>
                </ul>

                <div class="card-body d-flex justify-content-between">
                    <a href="{{ route('lokal.show', $appointment->service->business) }}" class="btn btn-outline-primary">Wróć do lokalu</a>
                    <a href="{{ route('szukaj') }}" class="btn btn-primary">Szukaj dalej</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
