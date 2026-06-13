@extends('layouts.app')

@section('title', 'Oceń specjalistę - BookMe')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Oceń specjalistę</h5>
                </div>
                <div class="card-body">

                    <p class="text-muted mb-1">
                        <i class="bi bi-person"></i> {{ $appointment->employee->name }}
                        — {{ $appointment->service->name }}
                    </p>
                    <p class="text-muted small mb-4">
                        <i class="bi bi-shop"></i> {{ $appointment->service->business->name }} •
                        {{ $appointment->start_at->translatedFormat('j F Y, H:i') }}
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

                    @if($review)
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-pencil"></i> Tego specjalistę już oceniłeś — możesz zaktualizować ocenę.
                        </div>
                    @endif

                    <form action="{{ route('klient.opinia.store', $appointment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Ocena *</label>
                            @include('partials.star-input', ['name' => 'rating', 'value' => $review->rating ?? ''])
                        </div>
                        <div class="mb-4">
                            <textarea name="comment" rows="3" class="form-control"
                                      placeholder="Komentarz (opcjonalnie)">{{ old('comment', $review->comment ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('klient.wizyty.index') }}" class="btn btn-secondary">Wróć</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Zapisz opinię
                            </button>
                        </div>
                    </form>

                    @if($review)
                        <hr class="my-4">
                        <h6 class="mb-2">Zdjęcia</h6>

                        @php
                            $myPhotos = $appointment->employee->reviewImages->filter(fn ($img) => $img->pivot->user_id == auth()->id());
                        @endphp
                        @if($myPhotos->count())
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($myPhotos as $img)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($img->file_name) }}"
                                         alt="zdjęcie opinii" class="rounded border" role="button"
                                         data-bs-toggle="modal" data-bs-target="#imageModal"
                                         data-img="{{ \Illuminate\Support\Facades\Storage::url($img->file_name) }}"
                                         style="width: 90px; height: 90px; object-fit: cover;">
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('klient.opinia.zdjecie', $appointment) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="photo" accept="image/*" class="form-control" required>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-image"></i> Dodaj zdjęcie
                                </button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('partials.star-input-assets')
@include('partials.image-modal')
@endsection
