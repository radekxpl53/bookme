@extends('layouts.app')

@section('title', 'Edycja Wizyty - Admin')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edycja Wizyty #{{ $appointment->id }}</h2>
                <a href="{{ route('admin.appointments') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Powrót
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Szczegóły wizyty</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Klient</label>
                                <input type="text" class="form-control" value="{{ $appointment->client ? $appointment->client->first_name . ' ' . $appointment->client->surname : 'Konto usunięte' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokal / Biznes</label>
                                <input type="text" class="form-control" value="{{ $appointment->employee->business->name ?? 'Brak' }}" disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Wizyty</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Oczekująca (pending)</option>
                                <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Zatwierdzona (confirmed)</option>
                                <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Zakończona (completed)</option>
                                <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Anulowana (cancelled)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Pracownik</label>
                                <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    @if($appointment->employee && $appointment->employee->business)
                                        @foreach($appointment->employee->business->employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id', $appointment->employee_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $appointment->employee_id }}" selected>Brak powiązanego biznesu</option>
                                    @endif
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="service_id" class="form-label">Usługa</label>
                                <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                                    @if($appointment->employee && $appointment->employee->business)
                                        @foreach($appointment->employee->business->services as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} ({{ $service->duration }} min) - {{ $service->price }} zł
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $appointment->service_id }}" selected>Brak powiązanego biznesu</option>
                                    @endif
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="appointment_date" class="form-label">Data wizyty</label>
                                <input type="date" name="appointment_date" id="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', $appointment->start_at->format('Y-m-d')) }}" required>
                                @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Godzina rozpoczęcia</label>
                                <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $appointment->start_at->format('H:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Notatki / Uwagi (opcjonalnie)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $appointment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Zapisz Zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
