@extends('layouts.app')

@section('title', 'Edycja lokalu - Admin')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edycja lokalu: {{ $business->name }}</h5>
                    <a href="{{ route('admin.businesses') }}" class="btn btn-sm btn-light">Powrót</a>
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

                    <form method="POST" action="{{ route('admin.businesses.update', $business) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nazwa salonu</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $business->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Kategoria</label>
                            <input type="text" class="form-control" id="category" name="category"
                                   value="{{ old('category', $business->category) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adres</label>
                            <input type="text" class="form-control" id="address" name="address"
                                   value="{{ old('address', $business->address) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Opis (opcjonalnie)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $business->description) }}</textarea>
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved', $business->is_approved) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_approved">Lokal zatwierdzony</label>
                            <small class="d-block text-muted">Odznacz, jeśli chcesz tymczasowo ukryć ten lokal przed klientami.</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.businesses') }}" class="btn btn-outline-secondary">Anuluj</a>
                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
