@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Moje Lokale</h2>
        <a href="{{ route('biznes.lokale.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Dodaj nowy lokal
        </a>
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
                    {{-- Iterujemy po lokalach Janusza --}}
                    @forelse($businesses as $business)
                        <tr>
                            <td class="align-middle fw-bold">{{ $business->name }}</td>
                            <td class="align-middle">{{ $business->address }}</td>
                            <td class="text-end">
                                <a href="{{ route('biznes.lokale.edit', $business->id) }}" class="btn btn-sm btn-outline-primary">
                                    Edytuj
                                </a>
                                <button class="btn btn-sm btn-outline-danger" disabled>Usuń</button>
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
