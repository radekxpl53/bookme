@extends('layouts.app')

@section('title', 'Zarządzanie Użytkownikami - Admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Użytkownicy w systemie</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Powrót
        </a>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $role === 'all' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'all']) }}">
                Wszyscy
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === 'owners' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'owners']) }}">
                Właściciele biznesów
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === 'clients' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'clients']) }}">
                Klienci
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === 'admins' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'admins']) }}">
                Administratorzy
            </a>
        </li>
    </ul>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Użytkownik</th>
                        <th>Kontakt</th>
                        <th>Typ konta</th>
                        <th>Dołączono</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="align-middle fw-bold">
                                {{ $user->first_name }} {{ $user->surname }}<br>
                                <small class="text-muted">@_{{ $user->username }}</small>
                            </td>
                            <td class="align-middle">
                                {{ $user->email }}<br>
                                <small class="text-muted">{{ $user->phone }}</small>
                            </td>
                            <td class="align-middle">
                                @if($user->isAdmin())
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->isOwner())
                                    <span class="badge bg-info text-dark">Właściciel ({{ $user->businesses_count }} lokali)</span>
                                @else
                                    <span class="badge bg-secondary">Klient</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ $user->created_at->format('d.m.Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Brak użytkowników.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
