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

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.businesses', ['status' => 'pending']) }}">
                Oczekujące na weryfikację
                @if($status === 'pending') <span class="badge bg-warning text-dark ms-1">{{ $businesses->count() }}</span> @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.businesses', ['status' => 'approved']) }}">
                Zatwierdzone
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.businesses', ['status' => 'all']) }}">
                Wszystkie
            </a>
        </li>
    </ul>

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
                                <form action="{{ route('admin.businesses.reject', $business) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz odrzucić i trwale usunąć ten lokal z systemu?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Odrzuć/Usuń">
                                        <i class="bi bi-trash"></i> {{ $business->is_approved ? 'Usuń' : 'Odrzuć' }}
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
