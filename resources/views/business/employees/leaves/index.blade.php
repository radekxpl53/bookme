@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold"><i class="bi bi-calendar-x text-warning me-2"></i>Urlopy Pracownika</h2>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    @if(Auth::user()->isAdmin())
                        <li class="breadcrumb-item"><a href="{{ route('admin.businesses.edit', $business) }}">Edycja lokalu</a></li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.index') }}">Moje lokale</a></li>
                    @endif
                    <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.pracownicy.index', $business) }}">Pracownicy</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $employee->name }} - Urlopy</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('biznes.lokale.pracownicy.index', $business) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Powrót
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Dodaj nowy urlop</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('biznes.lokale.pracownicy.urlopy.store', [$business, $employee]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Od dnia</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Do dnia (włącznie)</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Powód / Typ (Opcjonalnie)</label>
                            <input type="text" name="reason" class="form-control" placeholder="np. Urlop wypoczynkowy, L4" value="{{ old('reason') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Zapisz urlop</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul text-secondary me-2"></i>Zapisane nieobecności</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Okres</th>
                                <th>Powód</th>
                                <th class="text-end pe-4">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td class="align-middle ps-4 fw-bold">
                                        {{ $leave->start_date->format('d.m.Y') }} - {{ $leave->end_date->format('d.m.Y') }}
                                        @if($leave->start_date <= now() && $leave->end_date >= now())
                                            <span class="badge bg-warning text-dark ms-2">Trwa obecnie</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted">
                                        {{ $leave->reason ?? 'Brak podanego powodu' }}
                                    </td>
                                    <td class="align-middle text-end pe-4">
                                        <form action="{{ route('biznes.lokale.pracownicy.urlopy.destroy', [$business, $employee, $leave]) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten urlop? Pracownik znów będzie miał w te dni wolne terminy w kalendarzu.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń urlop">
                                                <i class="bi bi-trash"></i> Usuń
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-check display-6 mb-3 d-block text-secondary"></i>
                                        Brak zapisanych urlopów. Pracownik pracuje według standardowego harmonogramu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
