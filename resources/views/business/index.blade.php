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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa salonu</th>
                        <th>Adres</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businesses as $business)
                        <tr>
                            <td class="align-middle fw-bold">
                                {{ $business->name }} <br>
                                <span class="badge bg-secondary fw-normal">{{ $business->category }}</span>
                            </td>
                            <td class="align-middle">{{ $business->address }}</td>
                            <td class="text-end">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="{{ route('biznes.lokale.uslugi.index', $business->id) }}" class="btn btn-sm btn-light border" title="Zarządzaj usługami">
                                        <i class="bi bi-card-list text-primary"></i> Usługi
                                    </a>
                                    <a href="{{ route('biznes.lokale.pracownicy.index', $business->id) }}" class="btn btn-sm btn-light border" title="Zarządzaj pracownikami">
                                        <i class="bi bi-people text-info"></i> Pracownicy
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Opcje
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                Nie masz jeszcze żadnych dodanych lokali.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
