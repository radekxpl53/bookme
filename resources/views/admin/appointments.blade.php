@extends('layouts.app')

@section('title', 'Zarządzanie Wizytami - Admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Wszystkie wizyty w systemie</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Powrót
        </a>
    </div>

    <div class="row align-items-center mb-4 border-bottom pb-2 pb-md-0">
        <div class="col-md-7">
            <ul class="nav nav-tabs border-bottom-0 mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.appointments', ['status' => 'all', 'search' => request('search')]) }}">
                        Wszystkie
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.appointments', ['status' => 'pending', 'search' => request('search')]) }}">
                        Oczekujące
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'confirmed' ? 'active' : '' }}" href="{{ route('admin.appointments', ['status' => 'confirmed', 'search' => request('search')]) }}">
                        Zatwierdzone
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'completed' ? 'active' : '' }}" href="{{ route('admin.appointments', ['status' => 'completed', 'search' => request('search')]) }}">
                        Zakończone
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'cancelled' ? 'active' : '' }}" href="{{ route('admin.appointments', ['status' => 'cancelled', 'search' => request('search')]) }}">
                        Anulowane
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
            <form action="{{ route('admin.appointments') }}" method="GET" class="d-flex">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ $search ?? '' }}" placeholder="Szukaj klienta, salonu lub pracownika..." aria-label="Szukaj">
                    <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('admin.appointments', ['status' => $status]) }}" class="btn btn-outline-danger btn-sm" title="Wyczyść wyszukiwanie"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data i Czas</th>
                            <th>Klient</th>
                            <th>Salon & Pracownik</th>
                            <th>Usługa</th>
                            <th>Status</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="align-middle">
                                    <strong>{{ $appointment->start_at->format('d.m.Y') }}</strong><br>
                                    <small class="text-muted">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->finish_at->format('H:i') }}</small>
                                </td>
                                <td class="align-middle">
                                    @if($appointment->client)
                                        {{ $appointment->client->first_name }} {{ $appointment->client->surname }}<br>
                                        <small class="text-muted">{{ $appointment->client->email }}</small>
                                    @else
                                        <span class="text-muted">Konto usunięte</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    {{ $appointment->employee->business->name ?? 'Brak salonu' }}<br>
                                    <small class="text-muted">{{ $appointment->employee->name ?? 'Brak pracownika' }}</small>
                                </td>
                                <td class="align-middle">
                                    {{ $appointment->service->name ?? 'Brak usługi' }}<br>
                                    <small class="text-muted">{{ number_format($appointment->total_price, 2) }} zł</small>
                                </td>
                                <td class="align-middle">
                                    @if($appointment->status === 'pending')
                                        <span class="badge bg-warning text-dark">Oczekująca</span>
                                    @elseif($appointment->status === 'confirmed')
                                        <span class="badge bg-primary">Zatwierdzona</span>
                                    @elseif($appointment->status === 'completed')
                                        <span class="badge bg-success">Zakończona</span>
                                    @elseif($appointment->status === 'cancelled')
                                        <span class="badge bg-danger">Anulowana</span>
                                    @endif
                                </td>
                                <td class="align-middle text-end">
                                    <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn btn-sm btn-outline-primary" title="Edytuj">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz bezpowrotnie usunąć tę wizytę z systemu? Tej operacji nie można cofnąć.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń wizytę">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Brak wizyt pasujących do kryteriów.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
