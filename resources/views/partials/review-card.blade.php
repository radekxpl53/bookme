<div class="card shadow-sm mb-2">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <strong>{{ $review->user ? $review->user->first_name : 'Użytkownik' }}</strong>
            @include('partials.stars', ['rating' => $review->rating])
        </div>

        {{-- Nazwa uslugi - tylko opinie o pracowniku ja maja --}}
        @if(!empty($review->service))
            <div class="text-muted small mt-1">
                <i class="bi bi-scissors"></i> {{ $review->service }}
            </div>
        @endif

        @if($review->comment)
            <p class="mb-1 mt-2">{{ $review->comment }}</p>
        @endif

        <small class="text-muted">
            {{ $review->created_at ? $review->created_at->format('d.m.Y') : '' }}
        </small>
    </div>
</div>
