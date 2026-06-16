@extends('layouts.app')

@section('title', 'Usługi — ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Usługi</h2>
            <p class="text-muted mb-0">{{ $business->name }}</p>
        </div>
        <div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.businesses.edit', $business) }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Powrót do edycji
                </a>
            @else
                <a href="{{ route('biznes.lokale.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Moje lokale
                </a>
            @endif
            <a href="{{ route('biznes.lokale.uslugi.create', $business) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Dodaj usługę
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa usługi</th>
                        <th>Cena</th>
                        <th>Czas trwania</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td class="align-middle fw-bold">{{ $service->name }}</td>
                            <td class="align-middle">{{ number_format($service->price, 2) }} zł</td>
                            <td class="align-middle">{{ $service->duration_minutes }} min</td>
                            <td class="text-end">
                                <a href="{{ route('biznes.lokale.uslugi.edit', [$business, $service]) }}" class="btn btn-sm btn-outline-primary">
                                    Edytuj
                                </a>
                                <form action="{{ route('biznes.lokale.uslugi.destroy', [$business, $service]) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę usługę?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Ten lokal nie ma jeszcze żadnych usług.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
