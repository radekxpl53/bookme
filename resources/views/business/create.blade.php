@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
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
                            <label for="address" class="form-label">Pełny adres *</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Opis salonu</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <hr>
                        <h6 class="mb-3">Wskaż dokładną lokalizację na mapie *</h6>
                        <p class="text-muted small">Kliknij na mapę w miejscu, gdzie znajduje się Twój lokal. System automatycznie pobierze współrzędne.</p>

                        <div id="map" style="height: 350px; width: 100%; border-radius: 8px; border: 1px solid #ccc; z-index: 1;"></div>

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
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([52.069, 19.254], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var marker = null;

            var oldLat = document.getElementById('lat').value;
            var oldLon = document.getElementById('lon').value;

            if(oldLat && oldLon) {
                marker = L.marker([oldLat, oldLon]).addTo(map);
                map.setView([oldLat, oldLon], 15);
            }

            map.on('click', function(e) {
                var lat = e.latlng.lat;
                var lon = e.latlng.lng;

                if (marker !== null) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }
                document.getElementById('lat').value = lat.toFixed(7);
                document.getElementById('lon').value = lon.toFixed(7);
            });
        });
    </script>
@endsection
