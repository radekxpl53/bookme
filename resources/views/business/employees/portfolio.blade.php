@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">Portfolio: {{ $employee->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.index') }}">Moje lokale</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.pracownicy.index', $business) }}">Pracownicy</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Portfolio</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Dodaj zdjęcia pracownika (np. Przed/Po, realizacje)</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('biznes.lokale.pracownicy.portfolio.store', [$business, $employee]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="photos" class="form-label">Wybierz pliki (JPG, PNG, WEBP)</label>
                    <input class="form-control @error('photos.*') is-invalid @enderror" type="file" id="photos" name="photos[]" multiple accept="image/*" required>
                    @error('photos.*')
                        <div class="invalid-feedback">Wystąpił błąd z jednym z przesłanych plików. Upewnij się, że to obrazy i nie przekraczają 5MB.</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Wgraj zdjęcia</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Obecne portfolio</h5>
        </div>
        <div class="card-body">
            @if($photos->isEmpty())
                <p class="text-muted mb-0">Nie wgrano jeszcze żadnych zdjęć do portfolio tego pracownika.</p>
            @else
                <div class="row g-3">
                    @foreach($photos as $photo)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100">
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="Zdjęcie portfolio" style="object-fit: cover; height: 200px;">
                                <div class="card-footer bg-white text-center">
                                    <form action="{{ route('biznes.lokale.pracownicy.portfolio.destroy', [$business, $employee, $photo]) }}" method="POST" onsubmit="return confirm('Na pewno chcesz usunąć to zdjęcie?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="bi bi-trash"></i> Usuń
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
