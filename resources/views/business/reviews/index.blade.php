@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold"><i class="bi bi-star-fill text-warning me-2"></i>Opinie Klientów</h2>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.index') }}">Moje lokale</a></li>
                    <li class="breadcrumb-item text-muted">{{ $business->name }}</li>
                    <li class="breadcrumb-item active" aria-current="page">Opinie</li>
                </ol>
            </nav>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-dark border p-2 fs-6">
                Ogólna ocena salonu: <i class="bi bi-star-fill text-warning mx-1"></i> {{ $avgBusinessRating ? number_format($avgBusinessRating, 1) : 'Brak ocen' }}
            </span>
        </div>
    </div>


    <ul class="nav nav-tabs mb-4" id="reviewsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab" aria-controls="business" aria-selected="true">
                <i class="bi bi-shop me-1"></i> Opinie o salonie <span class="badge bg-secondary ms-1">{{ $businessReviews->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4" id="employee-tab" data-bs-toggle="tab" data-bs-target="#employee" type="button" role="tab" aria-controls="employee" aria-selected="false">
                <i class="bi bi-people me-1"></i> Opinie o pracownikach <span class="badge bg-secondary ms-1">{{ $employeeReviews->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reviewsTabsContent">

        <div class="tab-pane fade show active" id="business" role="tabpanel" aria-labelledby="business-tab">
            <div class="row g-4">
                @forelse($businessReviews as $review)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-muted"></i>{{ $review->user->first_name }} {{ $review->user->surname }}</h6>
                                    <div class="text-warning">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="bi bi-star{{ $review->rating >= $i ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="card-text fst-italic text-muted">"{{ $review->comment ?? 'Brak komentarza.' }}"</p>
                            </div>
                            <div class="card-footer bg-white text-muted small border-0 pt-0">
                                <i class="bi bi-clock me-1"></i> {{ $review->created_at->diffForHumans() }} ({{ $review->created_at->format('d.m.Y') }})
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border text-center py-5">
                            <i class="bi bi-chat-square-text display-4 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Ten salon nie posiada jeszcze żadnych opinii.</h5>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>


        <div class="tab-pane fade" id="employee" role="tabpanel" aria-labelledby="employee-tab">
            <div class="row g-4">
                @forelse($employeeReviews as $review)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-muted"></i>{{ $review->user->first_name }} {{ $review->user->surname }}</h6>
                                    <div class="text-warning">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="bi bi-star{{ $review->rating >= $i ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border me-1"><i class="bi bi-person-badge text-info me-1"></i> {{ $review->employee->name }}</span>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-scissors text-secondary me-1"></i> {{ $review->service }}</span>
                                </div>

                                <p class="card-text fst-italic text-muted mt-3">"{{ $review->comment ?? 'Brak komentarza.' }}"</p>
                            </div>
                            <div class="card-footer bg-white text-muted small border-0 pt-0">
                                <i class="bi bi-clock me-1"></i> {{ $review->created_at->diffForHumans() }} ({{ $review->created_at->format('d.m.Y') }})
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border text-center py-5">
                            <i class="bi bi-people display-4 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Twoi pracownicy nie posiadają jeszcze żadnych opinii.</h5>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
