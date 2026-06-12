@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Moje Lokale</h2>
        <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Dodaj nowy lokal
        </a>
    </div>

    <!-- Wyszukiwarka -->
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

    <!-- Lista biznesów - KAFELKI -->
    <div class="row g-4">
        @forelse($businesses as $business)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 hover-shadow transition-all">
                    <!-- Obrazek tła lub placeholder -->
                    <div class="card-img-top bg-dark text-white d-flex flex-column justify-content-end p-3 position-relative" style="height: 160px; background: linear-gradient(135deg, #2b2b2b, #4b4b4b);">
                        @if(!$business->is_approved)
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark shadow-sm"><i class="bi bi-hourglass-split"></i> Oczekuje na akceptację</span>
                            </div>
                        @endif
                        <h4 class="card-title fw-bold mb-1 text-truncate">{{ $business->name }}</h4>
                        <p class="card-text small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ Str::limit($business->address, 40) }}</p>
                        <span class="badge bg-light text-dark position-absolute top-0 start-0 m-3 shadow-sm">{{ $business->category }}</span>
                    </div>

                    <div class="card-body bg-white pt-4">
                        <!-- Główne Akcje (Duże Przyciski) -->
                        <div class="d-grid gap-2 mb-4">
                            <a href="{{ route('biznes.lokale.kalendarz.index', $business->id) }}" class="btn btn-primary fw-bold text-start shadow-sm">
                                <i class="bi bi-calendar-week me-2"></i> Kalendarz Wizyt
                            </a>
                        </div>
                        
                        <!-- Akcje Zarządzania -->
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.uslugi.index', $business->id) }}" class="btn btn-outline-dark w-100 text-start text-truncate">
                                    <i class="bi bi-scissors text-info me-1"></i> Usługi
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.pracownicy.index', $business->id) }}" class="btn btn-outline-dark w-100 text-start text-truncate">
                                    <i class="bi bi-people text-success me-1"></i> Zespół
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.opinie.index', $business->id) }}" class="btn btn-outline-dark w-100 text-start text-truncate">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Opinie
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('biznes.lokale.zdjecia.index', $business->id) }}" class="btn btn-outline-dark w-100 text-start text-truncate">
                                    <i class="bi bi-images text-primary me-1"></i> Zdjęcia
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opcje Dodatkowe w stopce karty -->
                    <div class="card-footer bg-light border-0 d-flex justify-content-between align-items-center p-3">
                        <div class="dropup w-100">
                            <button class="btn btn-secondary btn-sm w-100 dropdown-toggle text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span><i class="bi bi-gear-fill me-1"></i> Ustawienia</span>
                            </button>
                            <ul class="dropdown-menu w-100 shadow border-0">
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
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center p-5 shadow-sm border-0">
                    <i class="bi bi-shop display-1 text-muted mb-3 d-block"></i>
                    <h4 class="text-muted">Nie masz jeszcze żadnych dodanych lokali.</h4>
                    <p class="text-muted">Rozpocznij swoją działalność dodając pierwszy salon.</p>
                    <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary mt-3">
                        Dodaj swój pierwszy biznes
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15)!important;
        transform: translateY(-3px);
    }
    .transition-all {
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection
