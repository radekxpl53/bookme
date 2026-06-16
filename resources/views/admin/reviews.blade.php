@extends('layouts.app')

@section('title', 'Zarządzanie Opiniami - Admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Zarządzanie Opiniami</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Powrót
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="visually-hidden" for="search">Szukaj</label>
                    <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-search"></i></div>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Szukaj po treści, nazwie..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="type">
                        <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>Wszystkie typy</option>
                        <option value="business" {{ request('type') === 'business' ? 'selected' : '' }}>O lokalach</option>
                        <option value="employee" {{ request('type') === 'employee' ? 'selected' : '' }}>O pracownikach</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="rating">
                        <option value="">Każda ocena</option>
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} gwiazdek</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Filtruj</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cel opinii</th>
                        <th>Autor</th>
                        <th>Ocena</th>
                        <th style="width: 40%">Treść</th>
                        <th>Data dodania</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="align-middle">
                                <strong>{{ $review->target_name }}</strong><br>
                                <span class="badge {{ $review->review_type === 'business' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $review->review_type === 'business' ? 'Lokal' : 'Pracownik' }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($review->user)
                                    {{ $review->user->first_name }} {{ $review->user->surname }}<br>
                                    <small class="text-muted">{{ $review->user->email }}</small>
                                @else
                                    <span class="text-muted">Usunięty użytkownik</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="text-warning text-nowrap">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td class="align-middle text-break">
                                {{ Str::limit($review->comment, 100) }}
                            </td>
                            <td class="align-middle">
                                {{ $review->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="text-end align-middle">
                                <form action="{{ route('admin.reviews.destroy', ['type' => $review->review_type, 'id' => $review->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę opinię?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Usuń opinię">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Brak opinii spełniających kryteria wyszukiwania.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
