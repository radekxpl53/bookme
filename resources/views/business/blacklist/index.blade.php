@extends('layouts.app')

@section('title', 'Czarna lista — ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Czarna lista</h2>
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
            <a href="{{ route('biznes.lokale.blacklist.create', $business) }}" class="btn btn-dark">
                <i class="bi bi-person-x-fill"></i> Zablokuj klienta
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 border-top border-dark border-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Klient (email)</th>
                        <th>Powód blokady</th>
                        <th>Data zablokowania</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blacklist as $item)
                        <tr>
                            <td class="align-middle fw-bold">
                                {{ $item->user->first_name }} {{ $item->user->surname }}<br>
                                <small class="text-muted">{{ $item->user->email }}</small>
                            </td>
                            <td class="align-middle">
                                {{ $item->reason ?: 'Brak podanego powodu' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="text-end align-middle">
                                <form action="{{ route('biznes.lokale.blacklist.destroy', [$business, $item]) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz odblokować tego klienta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-success">Odblokuj</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-emoji-smile fs-1 d-block mb-3"></i>
                                Czarna lista jest pusta. Super!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
