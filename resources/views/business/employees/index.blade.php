@extends('layouts.app')

@section('title', 'Pracownicy — ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Pracownicy</h2>
            <p class="text-muted mb-0">{{ $business->name }}</p>
        </div>
        <div>
            <a href="{{ route('biznes.lokale.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Moje lokale
            </a>
            <a href="{{ route('biznes.lokale.pracownicy.create', $business) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Dodaj pracownika
            </a>
        </div>
    </div>

    <div class="row g-3">
        @forelse($employees as $employee)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">{{ $employee->name }}</h5>
                                @if($employee->specialization)
                                    <p class="text-muted small mb-2">{{ $employee->specialization }}</p>
                                @endif
                            </div>
                            <span class="badge {{ $employee->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $employee->is_active ? 'Aktywny' : 'Nieaktywny' }}
                            </span>
                        </div>

                        <p class="small mb-3">
                            <i class="bi bi-briefcase"></i> {{ $employee->services_count }} {{ $employee->services_count == 1 ? 'usługa' : 'usług' }}
                        </p>

                        <div class="d-flex gap-2">
                            <a href="{{ route('biznes.lokale.pracownicy.edit', [$business, $employee]) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                Edytuj
                            </a>
                            <form action="{{ route('biznes.lokale.pracownicy.destroy', [$business, $employee]) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tego pracownika?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-4 text-muted">
                        Ten lokal nie ma jeszcze żadnych pracowników.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
