@extends('layouts.app')

@section('title', 'Rezerwacja - '.$business->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h4 mb-1">Rezerwacja wizyty</h1>
                    <p class="text-muted mb-0"><i class="bi bi-shop"></i> {{ $business->name }}</p>
                </div>
            </div>

            <ul class="nav nav-pills nav-fill mb-4">
                @foreach(['Usługa', 'Termin', 'Potwierdzenie'] as $i => $label)
                    <li class="nav-item">
                        <span class="nav-link {{ $step === $i + 1 ? 'active' : ($step > $i + 1 ? 'text-success' : 'text-muted') }}">
                            {{ $i + 1 }}. {{ $label }}
                            @if($step > $i + 1)
                                <i class="bi bi-check-circle-fill"></i>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($step === 1)
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"><h6 class="mb-0">Wybierz usługę</h6></div>
                    <div class="card-body">
                        @forelse($business->services as $s)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <h6 class="mb-0">{{ $s->name }}</h6>
                                    <span class="text-muted small"><i class="bi bi-clock"></i> {{ $s->duration_minutes }} min</span>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold mb-1">{{ number_format($s->price, 2) }} zł</div>
                                    <a href="{{ route('rezerwacja.create', ['business' => $business, 'service_id' => $s->id]) }}"
                                       class="btn btn-outline-primary btn-sm">Wybierz</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Ten lokal nie ma jeszcze usług.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if($step === 2)
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Wybierz termin — {{ $service->name }}</h6>
                        <a href="{{ route('rezerwacja.create', $business) }}" class="btn btn-light btn-sm">Zmień usługę</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('rezerwacja.create', $business) }}" method="GET" class="row g-2 align-items-end mb-4">
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <div class="col-auto">
                                <label class="form-label mb-0">Dzień</label>
                                <input type="date" name="date" class="form-control form-control-sm"
                                       value="{{ $dzien->toDateString() }}" min="{{ now()->toDateString() }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm">Pokaż dostępność</button>
                            </div>
                        </form>

                        <p class="small text-muted mb-3">
                            Dostępność na {{ $dzien->translatedFormat('l, j F') }}:
                        </p>

                        @forelse($pracownicy as $e)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle fs-4 text-secondary me-2"></i>
                                    <div>
                                        <h6 class="mb-0">{{ $e->name }}</h6>
                                        <span class="text-muted small">{{ $e->specialization }}</span>
                                    </div>
                                </div>

                                @if(count($e->terminy) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($e->terminy as $termin)
                                            <a class="btn btn-outline-primary btn-sm"
                                               href="{{ route('rezerwacja.create', [
                                                    'business' => $business,
                                                    'service_id' => $service->id,
                                                    'employee_id' => $e->id,
                                                    'date' => $termin['time']->toDateString(),
                                                    'time' => $termin['time']->format('H:i'),
                                               ]) }}">
                                                {{ $termin['time']->format('H:i') }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted small mb-0">Brak wolnych terminów tego dnia.</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">Brak specjalistów wykonujących tę usługę.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if($step === 3)
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"><h6 class="mb-0">Potwierdź rezerwację</h6></div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Lokal</span><strong>{{ $business->name }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Usługa</span><strong>{{ $service->name }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Specjalista</span><strong>{{ $employee->name }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Termin</span>
                                <strong>{{ \Carbon\Carbon::parse($dzien->toDateString().' '.$time)->translatedFormat('l, j F Y, H:i') }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Czas trwania</span><strong>{{ $service->duration_minutes }} min</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Cena</span><strong class="text-primary">{{ number_format($service->price, 2) }} zł</strong>
                            </li>
                        </ul>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rezerwacja.create', ['business' => $business, 'service_id' => $service->id, 'date' => $dzien->toDateString()]) }}"
                               class="btn btn-secondary">Zmień termin</a>

                            <form action="{{ route('rezerwacja.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <input type="hidden" name="date" value="{{ $dzien->toDateString() }}">
                                <input type="hidden" name="time" value="{{ $time }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Potwierdź rezerwację
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
