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

        @if(!empty($images) && count($images))
            <div class="d-flex flex-wrap gap-2 my-2">
                @foreach($images as $img)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($img->file_name) }}"
                         alt="zdjęcie opinii" class="rounded border" role="button"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-img="{{ \Illuminate\Support\Facades\Storage::url($img->file_name) }}"
                         style="width: 80px; height: 80px; object-fit: cover;">
                @endforeach
            </div>
        @endif

        <small class="text-muted">
            {{ $review->created_at ? $review->created_at->format('d.m.Y') : '' }}
        </small>
    </div>
</div>
