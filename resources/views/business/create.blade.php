@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Dodaj nowy salon</h5>
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

            <form action="{{ route('biznes.lokale.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nazwa salonu *</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Kategoria salonu *</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Wybierz kategorię --</option>
                        <option value="Fryzjer" {{ old('category') == 'Fryzjer' ? 'selected' : '' }}>Fryzjer</option>
                        <option value="Barber" {{ old('category') == 'Barber' ? 'selected' : '' }}>Barber</option>
                        <option value="Kosmetyczka" {{ old('category') == 'Kosmetyczka' ? 'selected' : '' }}>Kosmetyczka</option>
                        <option value="Masaż" {{ old('category') == 'Masaż' ? 'selected' : '' }}>Masaż</option>
                        <option value="Paznokcie" {{ old('category') == 'Paznokcie' ? 'selected' : '' }}>Paznokcie</option>
                        <option value="Brwi i rzęsy" {{ old('category') == 'Brwi i rzęsy' ? 'selected' : '' }}>Brwi i rzęsy</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Pełny adres *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required placeholder="Wpisz miasto i ulicę">
                        <button type="button" class="btn btn-outline-secondary" id="geocodeBtn">
                            <i class="bi bi-geo-alt-fill"></i> Pokaż na mapie
                        </button>
                    </div>
                    <div class="form-text">Wpisz adres i kliknij "Pokaż na mapie", aby ustawić pinezką lokalizację.</div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Opis salonu</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>

                <hr>
                <h6 class="mb-3">Lokalizacja na mapie *</h6>
                <p class="text-muted small">Kliknij przycisk "Pokaż na mapie" lub kliknij bezpośrednio na mapę, aby ustawić pinezką.</p>

                <div id="map" style="height: 400px; width: 100%; border: 2px solid #ccc; border-radius: 8px; z-index: 1;"></div>

                <input type="hidden" id="lat" name="lat" value="{{ old('lat') }}">
                <input type="hidden" id="lon" name="lon" value="{{ old('lon') }}">

                <div class="mt-4 text-end">
                    <a href="{{ route('biznes.lokale.index') }}" class="btn btn-secondary">Anuluj</a>
                    <button type="submit" class="btn btn-success">Zapisz salon</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof L === 'undefined') {
            document.getElementById('map').innerHTML = "<h3 style='color:red; text-align:center; padding-top: 50px;'>Nie udało się załadować mapy. Wyłącz blokowanie skryptów w przeglądarce.</h3>";
            return;
        }

        var map = L.map('map').setView([52.069, 19.254], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker = null;
        var latInput = document.getElementById('lat');
        var lonInput = document.getElementById('lon');
        var addressInput = document.getElementById('address');
        var geocodeBtn = document.getElementById('geocodeBtn');

        if (latInput.value && lonInput.value && !isNaN(parseFloat(latInput.value))) {
            marker = L.marker([parseFloat(latInput.value), parseFloat(lonInput.value)]).addTo(map);
            map.setView([parseFloat(latInput.value), parseFloat(lonInput.value)], 15);
        }

        map.on('click', function(e) {
            if (marker !== null) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
            latInput.value = e.latlng.lat.toFixed(7);
            lonInput.value = e.latlng.lng.toFixed(7);
        });

        geocodeBtn.addEventListener('click', function() {
            var query = addressInput.value.trim();
            if (query.length < 3) {
                alert('Wpisz adres (min. 3 znaki), żeby wyszukać lokalizację.');
                return;
            }

            geocodeBtn.disabled = true;
            geocodeBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Szukam...';

            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data && data.length > 0) {
                        var newLat = parseFloat(data[0].lat);
                        var newLon = parseFloat(data[0].lon);
                        map.setView([newLat, newLon], 16);
                        if (marker !== null) {
                            marker.setLatLng([newLat, newLon]);
                        } else {
                            marker = L.marker([newLat, newLon]).addTo(map);
                        }
                        latInput.value = newLat.toFixed(7);
                        lonInput.value = newLon.toFixed(7);
                    } else {
                        alert('Nie znaleziono lokalizacji. Spróbuj wpisać dokładniejszy adres.');
                    }
                })
                .catch(function() {
                    alert('Błąd podczas wyszukiwania. Sprawdź połączenie z internetem.');
                })
                .finally(function() {
                    geocodeBtn.disabled = false;
                    geocodeBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Pokaż na mapie';
                });
        });
    });
</script>
@endsection
