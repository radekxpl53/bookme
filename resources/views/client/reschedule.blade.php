@extends('layouts.app')

@section('title', 'Zmień termin - BookMe')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zmień termin wizyty</h5>
                </div>
                <div class="card-body">

                    <p class="text-muted mb-1">
                        <i class="bi bi-scissors"></i> {{ $appointment->service->name }} — {{ $appointment->employee->name }}
                    </p>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-shop"></i> {{ $appointment->service->business->name }}
                    </p>
                    <p class="small mb-3">
                        Obecny termin: <strong>{{ $appointment->start_at->format('d.m.Y, H:i') }}</strong>
                    </p>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('klient.wizyty.zmien', $appointment) }}" method="GET" class="row g-2 align-items-end mb-4">
                        <div class="col-auto">
                            <label class="form-label mb-0">Dzień</label>
                            <input type="date" name="date" class="form-control form-control-sm"
                                   value="{{ $day->toDateString() }}" min="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">Pokaż terminy</button>
                        </div>
                    </form>

                    <p class="small text-muted mb-2">Wolne terminy na {{ $day->format('d.m.Y') }}:</p>

                    @if(count($slots) > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($slots as $slot)
                                <form action="{{ route('klient.wizyty.zmien.zapisz', $appointment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="date" value="{{ $slot['time']->toDateString() }}">
                                    <input type="hidden" name="time" value="{{ $slot['time']->format('H:i') }}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        {{ $slot['time']->format('H:i') }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Brak wolnych terminów tego dnia. Wybierz inną datę.</p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('klient.wizyty.index') }}" class="btn btn-secondary">Wróć</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
