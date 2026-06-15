<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-8">
                <h5 class="card-title mb-1">{{ $service->name }}</h5>
                <p class="mb-1">
                    <a href="{{ route('lokal.show', $service->business) }}" class="text-decoration-none fw-semibold">
                        {{ $service->business->name }}
                    </a>
                    <span class="badge bg-light text-dark border ms-1">{{ $service->business->category }}</span>
                </p>
                <p class="text-muted small mb-2">
                    <i class="bi bi-geo-alt"></i> {{ $service->business->address }}
                </p>

                <p class="mb-0">
                    @include('partials.stars', ['rating' => round($service->avg_rating ?? 0)])
                    <span class="fw-bold">
                        {{ $service->avg_rating ? number_format($service->avg_rating, 1) : '—' }}
                    </span>
                    <span class="text-muted small">({{ $service->reviews_count ?? 0 }} opinii)</span>
                </p>
            </div>

            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <p class="mb-1 text-muted small">
                    <i class="bi bi-clock"></i> {{ $service->duration_minutes }} min
                </p>
                <p class="fs-5 fw-bold text-primary mb-0">{{ number_format($service->price, 2) }} zł</p>
            </div>
        </div>

        <hr class="my-3">

        @php
            $bookingParams = [
                'business'   => $service->business_id,
                'service_id' => $service->id,
            ];
            $singleDay = $hasDateFilter && $dateFrom->toDateString() === $dateTo->toDateString();
            if ($singleDay) {
                $bookingParams['date'] = $dateFrom->toDateString();
            }
        @endphp

        @if($hasDateFilter)
            <p class="small mb-2 {{ $service->slotsCount > 0 ? 'text-success' : 'text-muted' }}">
                <i class="bi bi-calendar-check"></i>
                @if($service->slotsCount > 0)
                    Dostępne terminy
                    @if($singleDay)
                        — {{ $dateFrom->translatedFormat('j F') }}
                    @else
                        w wybranym zakresie dat
                    @endif
                    ({{ $service->slotsCount }}{{ $service->slotsCount >= 100 ? '+' : '' }}).
                @else
                    Brak wolnych terminów w wybranym zakresie.
                @endif
            </p>
        @endif

        <a href="{{ route('rezerwacja.create', $bookingParams) }}"
           class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus"></i> Umów wizytę
        </a>
    </div>
</div>
