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

        @php $multiDay = $dateFrom->toDateString() !== $dateTo->toDateString(); @endphp
        <div>
            <p class="small text-muted mb-2">
                <i class="bi bi-calendar-check"></i>
                @if($multiDay)
                    Najbliższe wolne terminy:
                @else
                    Wolne terminy na {{ $dateFrom->translatedFormat('l, j F') }}:
                @endif
            </p>

            @if(count($service->slots) > 0)
                <div class="d-flex flex-wrap gap-2">
                    @foreach($service->slots as $slot)
                        <a class="btn btn-outline-primary btn-sm"
                           href="{{ route('rezerwacja.create', [
                                'business'    => $service->business_id,
                                'service_id'  => $service->id,
                                'employee_id' => $slot['employee_id'],
                                'date'        => $slot['time']->toDateString(),
                                'time'        => $slot['time']->format('H:i'),
                           ]) }}"
                           title="{{ $slot['employee_name'] }}">
                            @if($multiDay)
                                {{ $slot['time']->translatedFormat('D j.m, H:i') }}
                            @else
                                {{ $slot['time']->format('H:i') }}
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0">
                    Brak wolnych terminów w wybranym zakresie. Zmień daty lub zobacz
                    <a href="{{ route('lokal.show', $service->business) }}">profil lokalu</a>.
                </p>
            @endif
        </div>
    </div>
</div>
