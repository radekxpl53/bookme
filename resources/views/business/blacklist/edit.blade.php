@extends('layouts.app')

@section('title', 'Edycja Blokady - ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edycja Blokady Klienta</h2>
                <a href="{{ route('biznes.lokale.blacklist.index', $business) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Powrót
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Aktualizuj powód zablokowania dla: {{ $blacklist->user->first_name }} {{ $blacklist->user->surname }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('biznes.lokale.blacklist.update', [$business, $blacklist]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="reason" class="form-label">Powód blokady (opcjonalnie)</label>
                            <input type="text" name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason', $blacklist->reason) }}" placeholder="Np. Notoryczne nieprzychodzenie na wizyty" maxlength="255">
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksymalnie 255 znaków. Informacja tylko dla Ciebie.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Zapisz Zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
