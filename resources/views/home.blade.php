@extends('layouts.app')

@section('title', 'BookMe - umów wizytę w salonie')

@section('content')
<div class="container">

    <div class="card shadow-sm mb-4">
        <div class="card-body text-center p-4">
            <h1 class="fw-bold mb-2">Znajdź i zarezerwuj wizytę w kilka chwil</h1>
            <p class="text-muted mb-4">Fryzjer, kosmetyczka, masaż — wszystko w jednym miejscu.</p>

            <form action="{{ route('szukaj') }}" method="GET" class="row g-2 justify-content-center">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control"
                           placeholder="Usługa lub nazwa lokalu (np. strzyżenie)">
                </div>
                <div class="col-md-3">
                    <input type="text" name="lokalizacja" class="form-control"
                           placeholder="Gdzie? (miasto)">
                </div>
                <div class="col-md-2">
                    <input type="date" name="data" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Szukaj</button>
                </div>
            </form>
        </div>
    </div>

    <h2 class="h4 mb-3">Przeglądaj według kategorii</h2>
    <div class="row g-3 mb-5">
        @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('szukaj', ['kategoria' => $category['name']]) }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="bi {{ $category['icon'] }} fs-2 text-primary"></i>
                            <p class="mt-2 mb-0">{{ $category['name'] }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <h2 class="h4 mb-3">Najpopularniejsze lokale</h2>
    <div class="row g-4 mb-5">
        @forelse($popularBusinesses as $business)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 position-relative">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h5 class="card-title mb-0">{{ $business->name }}</h5>
                            <span class="badge bg-light text-dark border ms-2">{{ $business->category }}</span>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt"></i> {{ $business->address }}
                        </p>

                        <p class="mb-1">
                            @include('partials.stars', ['rating' => round($business->reviews_avg_rating ?? 0)])
                            <span class="fw-bold">
                                {{ $business->reviews_avg_rating
                                    ? number_format($business->reviews_avg_rating, 1)
                                    : '—' }}
                            </span>
                            <span class="text-muted small">({{ $business->reviews_count }} opinii)</span>
                        </p>

                        @if($business->services_min_price)
                            <p class="small text-muted mb-0">
                                Usługi już od
                                <span class="fw-bold text-primary">{{ number_format($business->services_min_price, 0, ',', ' ') }} zł</span>
                            </p>
                        @endif

                        <div class="mt-auto pt-3">
                            <a href="{{ route('lokal.show', $business) }}" class="btn btn-outline-primary btn-sm w-100 stretched-link">Zobacz lokal</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted py-4">Nie ma jeszcze żadnych lokali.</p>
            </div>
        @endforelse
    </div>

    <h2 class="h4 mb-3 text-center">Jak to działa?</h2>
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <i class="bi bi-search fs-1 text-primary"></i>
            <h5 class="mt-2">1. Wybierz usługę</h5>
            <p class="text-muted small">Przeglądaj salony i usługi w swojej okolicy.</p>
        </div>
        <div class="col-md-4">
            <i class="bi bi-calendar-check fs-1 text-primary"></i>
            <h5 class="mt-2">2. Wybierz termin</h5>
            <p class="text-muted small">Zarezerwuj dogodną godzinę u wybranego specjalisty.</p>
        </div>
        <div class="col-md-4">
            <i class="bi bi-emoji-smile fs-1 text-primary"></i>
            <h5 class="mt-2">3. Przyjdź na wizytę</h5>
            <p class="text-muted small">Gotowe! Reszta to już przyjemność.</p>
        </div>
    </div>

</div>
@endsection
