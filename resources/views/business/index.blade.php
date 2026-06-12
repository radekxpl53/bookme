@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Moje Lokale</h2>
        <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Dodaj nowy lokal
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('biznes.lokale.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label visually-hidden">Szukaj</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Szukaj po nazwie lub adresie...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label visually-hidden">Kategoria</label>
                    <select class="form-select" id="category" name="category">
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
                    <button type="submit" class="btn btn-dark w-100">Filtruj</button>
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
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title fw-bold mb-0 text-truncate" title="{{ $business->name }}">
                                <i class="bi bi-shop text-primary me-2"></i>{{ $business->name }}
                            </h5>
                            @if(!$business->is_approved)
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Oczekuje</span>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border"><i class="bi bi-tag-fill text-secondary me-1"></i> {{ $business->category }}</span>
                        </div>
                        
                        <p class="card-text text-muted small mb-4">
                            <i class="bi bi-geo-alt-fill me-1"></i> {{ $business->address }}
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('biznes.lokale.kalendarz.index', $business->id) }}" class="btn btn-primary btn-sm flex-grow-1 fw-bold">
                                <i class="bi bi-calendar3"></i> Kalendarz
                            </a>
                            
                            <div class="btn-group flex-grow-1" role="group">
                                <a href="{{ route('biznes.lokale.uslugi.index', $business->id) }}" class="btn btn-light btn-sm border" title="Usługi">
                                    <i class="bi bi-card-list text-primary"></i>
                                </a>
                                <a href="{{ route('biznes.lokale.pracownicy.index', $business->id) }}" class="btn btn-light btn-sm border" title="Pracownicy">
                                    <i class="bi bi-people text-info"></i>
                                </a>
                                
                                <button type="button" class="btn btn-light btn-sm border dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Więcej
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('biznes.lokale.opinie.index', $business->id) }}">
                                            <i class="bi bi-star me-2 text-warning"></i> Opinie o salonie
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('biznes.lokale.zdjecia.index', $business->id) }}">
                                            <i class="bi bi-images me-2 text-primary"></i> Zdjęcia salonu
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('biznes.lokale.edit', $business->id) }}">
                                            <i class="bi bi-pencil-square me-2 text-secondary"></i> Edytuj lokal
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('biznes.lokale.blacklist.index', $business->id) }}">
                                            <i class="bi bi-person-x me-2 text-dark"></i> Czarna lista
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('biznes.lokale.destroy', $business->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten salon? Wszystkie dane zostaną trwale wykasowane!');">
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
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-center py-5">
                    <i class="bi bi-shop-window display-4 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Nie masz jeszcze żadnych dodanych lokali.</h5>
                    <p class="text-muted mb-4">Dodaj swój pierwszy salon, aby móc przyjmować rezerwacje.</p>
                    <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary">Dodaj nowy lokal</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
