@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edytuj salon: {{ $business->name }}</h5>
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

                    <form action="{{ route('biznes.lokale.update', $business->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nazwa salonu *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $business->name) }}" required>
                        </div>
                        <div class="mb-3">
                    <label for="category" class="form-label">Kategoria salonu *</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="Fryzjer" {{ old('category', $business->category) == 'Fryzjer' ? 'selected' : '' }}>Fryzjer</option>
                        <option value="Barber" {{ old('category', $business->category) == 'Barber' ? 'selected' : '' }}>Barber</option>
                        <option value="Kosmetyczka" {{ old('category', $business->category) == 'Kosmetyczka' ? 'selected' : '' }}>Kosmetyczka</option>
                        <option value="Masaż" {{ old('category', $business->category) == 'Masaż' ? 'selected' : '' }}>Masaż</option>
                        <option value="Paznokcie" {{ old('category', $business->category) == 'Paznokcie' ? 'selected' : '' }}>Paznokcie</option>
                        <option value="Brwi i rzęsy" {{ old('category', $business->category) == 'Brwi i rzęsy' ? 'selected' : '' }}>Brwi i rzęsy</option>
                    </select>
                </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Pełny adres *</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $business->address) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Opis salonu</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $business->description) }}</textarea>
                        </div>

                        <hr>
                        <h6 class="mb-3">Aktualna lokalizacja na mapie *</h6>
                        <div id="map" style="height: 350px; width: 100%; border-radius: 8px; border: 2px solid #ccc; z-index: 1;"></div>

                        <input type="hidden" id="lat" name="lat" value="{{ old('lat', $business->lat) }}">
                        <input type="hidden" id="lon" name="lon" value="{{ old('lon', $business->lon) }}">

                        <div class="mt-4 text-end">
                            <a href="{{ route('biznes.lokale.index') }}" class="btn btn-secondary">Anuluj</a>
                            <button type="submit" class="btn btn-success">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    setTimeout(function() {
        if (typeof L === 'undefined') {
            document.getElementById('map').innerHTML = "<h3 style='color:red; text-align:center; padding-top: 50px;'>BRAVE BLOKUJE SKRYPTY! Wyłącz Tarcze.</h3>";
            return;
        }

        var latInput = document.getElementById('lat');
        var lonInput = document.getElementById('lon');
        var addressInput = document.getElementById('address');

        var startLat = parseFloat(latInput.value);
        var startLon = parseFloat(lonInput.value);
        var defaultZoom = 15;
        var marker = null;

        if (isNaN(startLat) || isNaN(startLon) || latInput.value === '' || lonInput.value === '') {
            startLat = 52.069;
            startLon = 19.254;
            defaultZoom = 6;
        }

        var map = L.map('map').setView([startLat, startLon], defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        map.invalidateSize();

        if (defaultZoom === 15) {
            marker = L.marker([startLat, startLon]).addTo(map);
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

        addressInput.addEventListener('blur', function() {
            var query = this.value;
            if (query.length > 5) {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
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
                        }
                    });
            }
        });
    }, 300);
</script>
@endsection
