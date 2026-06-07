@extends('layouts.app')

@section('title', 'Dodaj pracownika — ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Dodaj pracownika — {{ $business->name }}</h5>
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

                    <form action="{{ route('biznes.lokale.pracownicy.store', $business) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Imię i nazwisko *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="specialization" class="form-label">Specjalizacja</label>
                            <input type="text" class="form-control" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="np. Fryzjer, Barber, Kosmetyczka">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktywny (widoczny dla klientów)</label>
                        </div>

                        @if($services->count() > 0)
                            <hr>
                            <h6 class="mb-3">Wykonywane usługi</h6>
                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="services[]" value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="service_{{ $service->id }}">
                                                {{ $service->name }} <span class="text-muted small">({{ number_format($service->price, 2) }} zł)</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <hr>
                        <h6 class="mb-3">Godziny pracy</h6>
                        @php
                            $days = [1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa', 4 => 'Czwartek', 5 => 'Piątek', 6 => 'Sobota', 7 => 'Niedziela'];
                        @endphp
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Dzień</th>
                                    <th>Od</th>
                                    <th>Do</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $num => $dayName)
                                    <tr>
                                        <td class="align-middle">{{ $dayName }}</td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm" name="working_hours[{{ $num }}][start_time]" value="{{ old("working_hours.{$num}.start_time") }}">
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm" name="working_hours[{{ $num }}][end_time]" value="{{ old("working_hours.{$num}.end_time") }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted small">Zostaw puste, jeśli pracownik nie pracuje danego dnia.</p>

                        <div class="text-end">
                            <a href="{{ route('biznes.lokale.pracownicy.index', $business) }}" class="btn btn-secondary">Anuluj</a>
                            <button type="submit" class="btn btn-success">Dodaj pracownika</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
