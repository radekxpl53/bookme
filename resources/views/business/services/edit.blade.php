@extends('layouts.app')

@section('title', 'Edytuj usługę — ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edytuj usługę — {{ $business->name }}</h5>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('biznes.lokale.uslugi.update', [$business, $service]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nazwa usługi *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Cena (zł) *</label>
                            <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $service->price) }}" required min="0" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="duration_minutes" class="form-label">Czas trwania (minuty) *</label>
                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" required min="5" max="480" step="5">
                        </div>

                        <div class="text-end">
                            <a href="{{ route('biznes.lokale.uslugi.index', $business) }}" class="btn btn-secondary">Anuluj</a>
                            <button type="submit" class="btn btn-success">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
