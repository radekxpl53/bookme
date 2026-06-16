@extends('layouts.app')

@section('title', 'Edycja Opinii - Admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edycja Opinii ({{ $type === 'business' ? 'Lokal' : 'Pracownik' }} - {{ $targetName }})</h2>
        <a href="{{ route('admin.reviews') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Wróć
        </a>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.reviews.update', ['type' => $type, 'id' => $review->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Ocena (Ilość gwiazdek) <span class="text-danger">*</span></label>
                    <div id="star-rating" class="fs-3 text-warning" style="cursor: pointer;">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ old('rating', $review->rating) >= $i ? '-fill' : '' }}" data-value="{{ $i }}"></i>
                        @endfor
                    </div>
                    <input type="hidden" id="rating" name="rating" value="{{ old('rating', $review->rating) }}" required>
                    @error('rating')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="comment" class="form-label">Komentarz</label>
                    <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="5">{{ old('comment', $review->comment) }}</textarea>
                    @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-bold">Zapisz Zmiany</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#star-rating i');
        const ratingInput = document.getElementById('rating');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-value');
                ratingInput.value = rating;
                updateStars(rating);
            });

            star.addEventListener('mouseover', function() {
                updateStars(this.getAttribute('data-value'));
            });

            star.addEventListener('mouseout', function() {
                updateStars(ratingInput.value || 0);
            });
        });

        function updateStars(rating) {
            stars.forEach(star => {
                if (star.getAttribute('data-value') <= rating) {
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            });
        }
    });
</script>
@endsection
