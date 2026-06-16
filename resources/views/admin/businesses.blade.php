@extends('layouts.app')

@section('title', 'Zarządzanie Lokalami - Admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Zarządzanie Lokalami</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Powrót
        </a>
    </div>

    <div class="row align-items-center mb-4 border-bottom pb-2 pb-md-0">
        <div class="col-md-7">
            <ul class="nav nav-tabs border-bottom-0 mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.businesses', ['status' => 'pending', 'search' => request('search')]) }}">
                        Oczekujące na weryfikację
                        @if($status === 'pending') <span class="badge bg-warning text-dark ms-1">{{ $businesses->count() }}</span> @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.businesses', ['status' => 'approved', 'search' => request('search')]) }}">
                        Zatwierdzone
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.businesses', ['status' => 'all', 'search' => request('search')]) }}">
                        Wszystkie
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
            <form action="{{ route('admin.businesses') }}" method="GET" class="d-flex">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ $search ?? '' }}" placeholder="Szukaj nazwy, miasta, adresu..." aria-label="Szukaj">
                    <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('admin.businesses', ['status' => $status]) }}" class="btn btn-outline-danger btn-sm" title="Wyczyść wyszukiwanie"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa salonu</th>
                        <th>Właściciel</th>
                        <th>Kategoria</th>
                        <th>Status</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businesses as $business)
                        <tr>
                            <td class="align-middle fw-bold">
                                {{ $business->name }} <br>
                                <small class="text-muted">{{ $business->address }}</small>
                            </td>
                            <td class="align-middle">
                                {{ $business->owner->first_name }} {{ $business->owner->surname }}<br>
                                <small class="text-muted">{{ $business->owner->email }}</small>
                            </td>
                            <td class="align-middle">{{ $business->category }}</td>
                            <td class="align-middle">
                                @if($business->is_approved)
                                    <span class="badge bg-success">Zatwierdzony</span>
                                @else
                                    <span class="badge bg-warning text-dark">Oczekujący</span>
                                @endif
                            </td>
                            <td class="text-end align-middle">
                                @if(!$business->is_approved)
                                    <form action="{{ route('admin.businesses.approve', $business) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Zatwierdź">
                                            <i class="bi bi-check-circle"></i> Zatwierdź
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.businesses.edit', $business) }}" class="btn btn-sm btn-primary" title="Edytuj">
                                    <i class="bi bi-pencil"></i> Edytuj
                                </a>
                                <form action="{{ route('admin.businesses.destroy', $business) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć ten lokal z systemu?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Usuń">
                                        <i class="bi bi-trash"></i> Usuń
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                Brak lokali w tej kategorii.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
