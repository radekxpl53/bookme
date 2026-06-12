@extends('layouts.app')

@section('title', $business->name . ' - BookMe')

@section('content')
<div class="container">

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
            @forelse($business->employees as $employee)
                @php
                    $empAvg = $employee->reviews->avg('rating');
                    $empCount = $employee->reviews->count();
                @endphp
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person-circle fs-1 text-secondary me-3"></i>
                            <div>
                                <h6 class="mb-0">{{ $employee->name }}</h6>
                                <p class="text-muted small mb-1">{{ $employee->specialization }}</p>
                                <span>
                                    @include('partials.stars', ['rating' => round($empAvg ?? 0)])
                                    <span class="fw-bold">{{ $empAvg ? number_format($empAvg, 1) : '—' }}</span>
                                    <span class="text-muted small">({{ $empCount }} opinii)</span>
                                </span>
                            </div>
                        </div>

                        @if($empCount > 0)
                            <hr>
                            @foreach($employee->reviews as $review)
                                @include('partials.review-card', [
                                    'review' => $review,
                                    'images' => $employee->reviewImages->filter(fn ($img) => $img->pivot->user_id == $review->user_id),
                                ])
                            @endforeach
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted py-3">Brak pracowników do wyświetlenia.</p>
            @endforelse
        </div>

        <div class="tab-pane fade" id="reviews" role="tabpanel">

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @auth
                        <h6 class="mb-3">{{ $mojaOpinia ? 'Twoja opinia o lokalu' : 'Dodaj opinię o lokalu' }}</h6>

                        @if($mojaOpinia)
                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-pencil"></i> Już oceniłeś ten lokal — możesz zaktualizować ocenę.
                            </div>
                        @endif

                        <form action="{{ route('lokal.opinia.store', $business) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                @include('partials.star-input', ['name' => 'rating', 'value' => $mojaOpinia->rating ?? ''])
                            </div>
                            <textarea name="comment" rows="2" class="form-control mb-2"
                                      placeholder="Komentarz (opcjonalnie)">{{ old('comment', $mojaOpinia->comment ?? '') }}</textarea>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-send"></i> Zapisz opinię
                            </button>
                        </form>

                        @if($mojaOpinia)
                            <hr>
                            @php
                                $mojeZdjecia = $business->reviewImages->filter(fn ($img) => $img->pivot->user_id == auth()->id());
                            @endphp
                            @if($mojeZdjecia->count())
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($mojeZdjecia as $img)
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
