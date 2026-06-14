@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Moje Lokale</h2>
        <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Dodaj nowy lokal
        </a>
    </div>


    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body bg-light rounded">
            <form action="{{ route('biznes.lokale.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label visually-hidden">Szukaj</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-0" id="search" name="search" value="{{ request('search') }}" placeholder="Szukaj po nazwie lub adresie...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label visually-hidden">Kategoria</label>
                    <select class="form-select border-0" id="category" name="category">
                        <option value="">Wszystkie kategorie</option>
                        <option value="Fryzjer" {{ request('category') == 'Fryzjer' ? 'selected' : '' }}>Fryzjer</option>
                        <option value="Barber" {{ request('category') == 'Barber' ? 'selected' : '' }}>Barber</option>
                        <option value="Kosmetyczka" {{ request('category') == 'Kosmetyczka' ? 'selected' : '' }}>Kosmetyczka</option>
                        <option value="Masaż" {{ request('category') == 'Masaż' ? 'selected' : '' }}>Masaż</option>
                        <option value="Paznokcie" {{ request('category') == 'Paznokcie' ? 'selected' : '' }}>Paznokcie</option>
                        <option value="Brwi i rzęsy" {{ request('category') == 'Brwi i rzęsy' ? 'selected' : '' }}>Brwi i rzęsy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100 shadow-sm">Filtruj</button>
                </div>
            </form>
            @if(request()->hasAny(['search', 'category']))
                <div class="mt-2 text-end">
                    <a href="{{ route('biznes.lokale.index') }}" class="btn btn-sm btn-outline-secondary">Wyczyść filtry</a>
                </div>
            @endif
        </div>
    </div>


    <div class="row g-4">
        @forelse($businesses as $business)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm custom-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title fw-bold mb-2 text-dark text-truncate" style="max-width: 200px;" title="{{ $business->name }}">{{ $business->name }}</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill border border-primary-subtle">{{ $business->category }}</span>
                                @if(!$business->is_approved)
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill"><i class="bi bi-hourglass-split"></i> Oczekuje</span>
                                @endif
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle shadow-none text-muted d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; padding: 0;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('biznes.lokale.edit', $business->id) }}">
                                            <i class="bi bi-pencil-square me-2 text-secondary"></i> Edytuj profil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('biznes.lokale.blacklist.index', $business->id) }}">
                                            <i class="bi bi-person-x me-2 text-dark"></i> Czarna lista
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('biznes.lokale.destroy', $business->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten salon? Wszystkie dane zostaną wykasowane!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Usuń lokal
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <p class="text-muted small mb-4"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ Str::limit($business->address, 45) }}</p>


                        <div class="d-grid mb-4">
                            <a href="{{ route('biznes.lokale.kalendarz.index', $business->id) }}" class="btn btn-primary rounded-3 fw-medium py-2 shadow-sm d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-check me-2 fs-5"></i> Kalendarz Wizyt
                            </a>
                        </div>
                        

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.uslugi.index', $business->id) }}" class="btn btn-light w-100 text-center rounded-3 text-secondary custom-btn-hover py-2">
                                    <i class="bi bi-scissors me-2 text-info"></i> Usługi
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.pracownicy.index', $business->id) }}" class="btn btn-light w-100 text-center rounded-3 text-secondary custom-btn-hover py-2">
                                    <i class="bi bi-people me-2 text-success"></i> Zespół
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.opinie.index', $business->id) }}" class="btn btn-light w-100 text-center rounded-3 text-secondary custom-btn-hover py-2">
                                    <i class="bi bi-star-fill me-2 text-warning"></i> Opinie
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.zdjecia.index', $business->id) }}" class="btn btn-light w-100 text-center rounded-3 text-secondary custom-btn-hover py-2">
                                    <i class="bi bi-images me-2 text-primary"></i> Zdjęcia
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center p-5 shadow-sm border-0 rounded-4">
                    <i class="bi bi-shop display-1 text-muted mb-3 d-block"></i>
                    <h4 class="text-muted">Nie masz jeszcze żadnych dodanych lokali.</h4>
                    <p class="text-muted">Rozpocznij swoją działalność dodając pierwszy salon.</p>
                    <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary mt-3 px-4 rounded-pill">
                        Dodaj swój pierwszy biznes
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .custom-card {
        border-radius: 1.25rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background-color: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    .custom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .custom-btn-hover {
        transition: all 0.2s ease-in-out;
        border: 1px solid #e2e8f0;
        background-color: #f8f9fa;
        font-size: 0.9rem;
    }
    .custom-btn-hover:hover {
        background-color: #e9ecef;
        border-color: #cbd5e1;
        color: #212529 !important;
    }
    .custom-btn-hover i {
        font-size: 1.1rem;
        vertical-align: middle;
    }
</style>
@endsection
