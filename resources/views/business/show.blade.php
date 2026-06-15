@extends('layouts.app')

@section('title', $business->name . ' - BookMe')

@section('styles')
<style>
    .business-carousel .carousel-control-prev,
    .business-carousel .carousel-control-next {
        width: auto;
        padding: 0 0.75rem;
        opacity: 1;
    }

    .business-carousel .carousel-control-prev-icon,
    .business-carousel .carousel-control-next-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 50%;
        background-color: rgba(33, 37, 41, 0.8);
        background-size: 45% 45%;
    }

    .business-carousel .carousel-indicators [data-bs-target] {
        background-color: #212529;
        opacity: 0.45;
    }

    .business-carousel .carousel-indicators .active {
        opacity: 0.9;
    }
</style>
@endsection

@section('content')
<div class="container">

    @if($business->photos->isNotEmpty())
        <div id="businessCarousel" class="carousel slide carousel-fade business-carousel mb-4 rounded shadow-sm overflow-hidden"
             data-bs-ride="carousel">
            @if($business->photos->count() > 1)
                <div class="carousel-indicators">
                    @foreach($business->photos as $photo)
                        <button type="button"
                                data-bs-target="#businessCarousel"
                                data-bs-slide-to="{{ $loop->index }}"
                                class="{{ $loop->first ? 'active' : '' }}"
                                aria-label="Zdjęcie {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif

            <div class="carousel-inner">
                @foreach($business->photos as $photo)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $photo->path) }}"
                             class="d-block w-100"
                             alt="{{ $business->name }} — zdjęcie {{ $loop->iteration }}"
                             style="height: min(360px, 50vw); object-fit: cover;">
                    </div>
                @endforeach
            </div>

            @if($business->photos->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#businessCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Poprzednie</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#businessCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Następne</span>
                </button>
            @endif
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-1">{{ $business->name }}</h1>
                    <p class="text-muted mb-2">
                        <i class="bi bi-geo-alt"></i> {{ $business->address }}
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span class="fw-bold">
                            {{ $averageRating ? number_format($averageRating, 1) : '—' }}
                        </span>
                        <span class="text-muted small">({{ $reviewsCount }} opinii)</span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('rezerwacja.create', $business) }}" class="btn btn-primary">
                        <i class="bi bi-calendar-check"></i> Zarezerwuj wizytę
                    </a>
                </div>
            </div>

            @if($business->description)
                <hr>
                <p class="mb-0">{{ $business->description }}</p>
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="businessTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="services-tab" data-bs-toggle="tab"
                    data-bs-target="#services" type="button" role="tab">
                Usługi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="employees-tab" data-bs-toggle="tab"
                    data-bs-target="#employees" type="button" role="tab">
                Pracownicy
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                    data-bs-target="#reviews" type="button" role="tab">
                Opinie ({{ $reviewsCount }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="location-tab" data-bs-toggle="tab"
                    data-bs-target="#location" type="button" role="tab">
                Lokalizacja
            </button>
        </li>
    </ul>

    <div class="tab-content mb-5">

        <div class="tab-pane fade show active" id="services" role="tabpanel">
            @forelse($business->services as $service)
                <div class="card shadow-sm mb-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $service->name }}</h6>
                            <span class="text-muted small">
                                <i class="bi bi-clock"></i> {{ $service->duration_minutes }} min
                            </span>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold mb-1">{{ number_format($service->price, 2) }} zł</div>
                            <a href="{{ route('rezerwacja.create', ['business' => $business, 'service_id' => $service->id]) }}" class="btn btn-outline-primary btn-sm">Wybierz</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted py-3">Ten lokal nie dodał jeszcze żadnych usług.</p>
            @endforelse
        </div>

        <div class="tab-pane fade" id="employees" role="tabpanel">
            @if($business->employees->isEmpty())
                <p class="text-muted py-3">Brak pracowników do wyświetlenia.</p>
            @else
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="bi bi-people"></i> Zespół</h6>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($business->employees as $employee)
                                    @php
                                        $empAvg = $employee->reviews->avg('rating');
                                        $empCount = $employee->reviews->count();
                                    @endphp
                                    <button type="button"
                                            class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 employee-picker {{ $loop->first ? 'bg-light border-start border-primary border-3' : '' }}"
                                            data-panel="employee-panel-{{ $employee->id }}">
                                        <span class="rounded-circle bg-body-secondary d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                              style="width: 48px; height: 48px;">
                                            <i class="bi bi-person fs-4 text-secondary"></i>
                                        </span>
                                        <span class="flex-grow-1 text-start min-w-0">
                                            <span class="d-block fw-semibold text-truncate">{{ $employee->name }}</span>
                                            @if($employee->specialization)
                                                <span class="d-block small text-muted text-truncate">{{ $employee->specialization }}</span>
                                            @endif
                                            <span class="d-block small mt-1">
                                                @include('partials.stars', ['rating' => round($empAvg ?? 0)])
                                                <span class="fw-bold">{{ $empAvg ? number_format($empAvg, 1) : '—' }}</span>
                                                <span class="text-muted">({{ $empCount }})</span>
                                            </span>
                                        </span>
                                        <i class="bi bi-chevron-right text-muted flex-shrink-0"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        @foreach($business->employees as $employee)
                            @php
                                $empAvg = $employee->reviews->avg('rating');
                                $empCount = $employee->reviews->count();
                            @endphp
                            <div id="employee-panel-{{ $employee->id }}"
                                 class="employee-panel {{ $loop->first ? '' : 'd-none' }}">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                                        <div>
                                            <h5 class="mb-1">{{ $employee->name }}</h5>
                                            @if($employee->specialization)
                                                <span class="badge bg-light text-dark border">{{ $employee->specialization }}</span>
                                            @endif
                                        </div>
                                        <div class="small">
                                            @include('partials.stars', ['rating' => round($empAvg ?? 0)])
                                            <span class="fw-bold">{{ $empAvg ? number_format($empAvg, 1) : '—' }}</span>
                                            <span class="text-muted">({{ $empCount }} opinii)</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($employee->portfolio->isNotEmpty())
                                            <h6 class="text-muted text-uppercase small fw-semibold mb-3">Portfolio</h6>
                                            <div id="portfolioCarousel{{ $employee->id }}"
                                                 class="carousel slide business-carousel rounded border overflow-hidden mb-4">
                                                @if($employee->portfolio->count() > 1)
                                                    <div class="carousel-indicators mb-0">
                                                        @foreach($employee->portfolio as $item)
                                                            <button type="button"
                                                                    data-bs-target="#portfolioCarousel{{ $employee->id }}"
                                                                    data-bs-slide-to="{{ $loop->index }}"
                                                                    class="{{ $loop->first ? 'active' : '' }}"
                                                                    aria-label="Zdjęcie {{ $loop->iteration }}"></button>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="carousel-inner">
                                                    @foreach($employee->portfolio as $item)
                                                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                            <img src="{{ asset('storage/' . $item->path) }}"
                                                                 alt="Portfolio {{ $employee->name }}"
                                                                 class="d-block w-100"
                                                                 role="button"
                                                                 data-bs-toggle="modal"
                                                                 data-bs-target="#imageModal"
                                                                 data-img="{{ asset('storage/' . $item->path) }}"
                                                                 style="height: 280px; object-fit: cover;">
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if($employee->portfolio->count() > 1)
                                                    <button class="carousel-control-prev" type="button"
                                                            data-bs-target="#portfolioCarousel{{ $employee->id }}"
                                                            data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Poprzednie</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                            data-bs-target="#portfolioCarousel{{ $employee->id }}"
                                                            data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Następne</span>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif

                                        <h6 class="text-muted text-uppercase small fw-semibold mb-3">Opinie</h6>

                                        @forelse($employee->reviews as $review)
                                            @include('partials.review-card', [
                                                'review' => $review,
                                                'images' => $employee->reviewImages->filter(fn ($img) => $img->pivot->user_id == $review->user_id),
                                            ])
                                        @empty
                                            <p class="text-muted small mb-0">Ten pracownik nie ma jeszcze opinii.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="tab-pane fade" id="reviews" role="tabpanel">

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @auth
                        <h6 class="mb-3">{{ $myReview ? 'Twoja opinia o lokalu' : 'Dodaj opinię o lokalu' }}</h6>

                        @if($myReview)
                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-pencil"></i> Już oceniłeś ten lokal — możesz zaktualizować ocenę.
                            </div>
                        @endif

                        <form action="{{ route('lokal.opinia.store', $business) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                @include('partials.star-input', ['name' => 'rating', 'value' => $myReview->rating ?? ''])
                            </div>
                            <textarea name="comment" rows="2" class="form-control mb-2"
                                      placeholder="Komentarz (opcjonalnie)">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-send"></i> Zapisz opinię
                            </button>
                        </form>

                        @if($myReview)
                            <hr>
                            @php
                                $myPhotos = $business->reviewImages->filter(fn ($img) => $img->pivot->user_id == auth()->id());
                            @endphp
                            @if($myPhotos->count())
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($myPhotos as $img)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($img->file_name) }}"
                                             alt="zdjęcie opinii" class="rounded border" role="button"
                                             data-bs-toggle="modal" data-bs-target="#imageModal"
                                             data-img="{{ \Illuminate\Support\Facades\Storage::url($img->file_name) }}"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @endforeach
                                </div>
                            @endif
                            <form action="{{ route('lokal.opinia.zdjecie', $business) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="file" name="photo" accept="image/*" class="form-control" required>
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-image"></i> Dodaj zdjęcie
                                    </button>
                                </div>
                            </form>
                        @endif
                    @else
                        <p class="mb-0 text-muted">
                            <a href="{{ route('login') }}">Zaloguj się</a>, aby dodać opinię o lokalu.
                        </p>
                    @endauth
                </div>
            </div>

            @forelse($business->reviews as $review)
                @include('partials.review-card', [
                    'review' => $review,
                    'images' => $business->reviewImages->filter(fn ($img) => $img->pivot->user_id == $review->user_id),
                ])
            @empty
                <p class="text-muted py-3">Ten lokal nie ma jeszcze żadnych opinii.</p>
            @endforelse
        </div>

        <div class="tab-pane fade" id="location" role="tabpanel">
            <p class="mb-2"><i class="bi bi-geo-alt"></i> {{ $business->address }}</p>

            @if($business->lat && $business->lon)
                <div id="map" style="height: 400px; border-radius: 8px; border: 1px solid #ccc;"></div>
            @else
                <p class="text-muted">Brak danych o lokalizacji na mapie.</p>
            @endif
        </div>

    </div>

</div>
@endsection

@section('scripts')
@include('partials.star-input-assets')
@include('partials.image-modal')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var selectedClasses = ['bg-light', 'border-start', 'border-primary', 'border-3'];

        document.querySelectorAll('.employee-picker').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('.employee-picker').forEach(function (btn) {
                    selectedClasses.forEach(function (cls) { btn.classList.remove(cls); });
                });
                document.querySelectorAll('.employee-panel').forEach(function (panel) {
                    panel.classList.add('d-none');
                });

                selectedClasses.forEach(function (cls) { button.classList.add(cls); });
                document.getElementById(button.dataset.panel).classList.remove('d-none');
            });
        });
    });
</script>
@if($business->lat && $business->lon)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapInitialized = false;

        document.getElementById('location-tab').addEventListener('shown.bs.tab', function () {
            if (mapInitialized) return;
            mapInitialized = true;

            var lat = {{ $business->lat }};
            var lon = {{ $business->lon }};

            var map = L.map('map').setView([lat, lon], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.marker([lat, lon]).addTo(map)
                .bindPopup({!! json_encode($business->name) !!});
        });
    });
</script>
@endif
@endsection
