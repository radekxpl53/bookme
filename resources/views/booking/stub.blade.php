@extends('layouts.app')

@section('title', 'Rezerwacja - BookMe')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Podsumowanie rezerwacji</h5>
                </div>
                <div class="card-body">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Trwają prace nad finalizacją rezerwacji. Poniżej wybrany termin:
                    </div>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Lokal</span>
                            <strong>{{ $service->business->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Usługa</span>
                            <strong>{{ $service->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Specjalista</span>
                            <strong>{{ $employee->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Termin</span>
                            <strong>{{ \Carbon\Carbon::parse($termin)->translatedFormat('l, j F Y, H:i') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Cena</span>
                            <strong class="text-primary">{{ number_format($service->price, 2) }} zł</strong>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Wróć do wyników</a>
                        <button type="button" class="btn btn-success" disabled>
                            Potwierdź rezerwację (wkrótce)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
