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

    <div class="row align-items-center mb-4 border-bottom pb-2 pb-md-0">
        <div class="col-md-7">
            <ul class="nav nav-tabs border-bottom-0 mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ $role === 'all' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'all', 'search' => request('search')]) }}">
                        Wszyscy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $role === 'owners' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'owners', 'search' => request('search')]) }}">
                        Właściciele biznesów
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $role === 'clients' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'clients', 'search' => request('search')]) }}">
                        Klienci
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $role === 'admins' ? 'active' : '' }}" href="{{ route('admin.users', ['role' => 'admins', 'search' => request('search')]) }}">
                        Administratorzy
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
            <form action="{{ route('admin.users') }}" method="GET" class="d-flex">
                <input type="hidden" name="role" value="{{ $role }}">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ $search ?? '' }}" placeholder="Szukaj imienia, email, username..." aria-label="Szukaj">
                    <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('admin.users', ['role' => $role]) }}" class="btn btn-outline-danger btn-sm" title="Wyczyść wyszukiwanie"><i class="bi bi-x-lg"></i></a>
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
                        <th>Użytkownik</th>
                        <th>Kontakt</th>
                        <th>Typ konta</th>
                        <th>Dołączono</th>
                        <th class="text-end">Akcje</th>
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
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edytuj">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Tej operacji nie można cofnąć.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
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
