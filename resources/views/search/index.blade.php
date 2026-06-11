@extends('layouts.app')

@section('title', 'Wyszukiwarka usług - BookMe')

@section('content')
<div class="container">

    <div class="row g-4">

        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> Filtry</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('szukaj') }}" method="GET" id="filtryForm">

                        <div class="mb-3">
                            <label class="form-label">Usługa</label>
                            <input type="text" name="usluga" class="form-control form-control-sm"
                                   value="{{ request('usluga') }}" placeholder="np. strzyżenie">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokalizacja</label>
                            <input type="text" name="lokalizacja" class="form-control form-control-sm"
                                   value="{{ request('lokalizacja') }}" placeholder="miasto / ulica">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategoria</label>
                            <select name="kategoria" class="form-select form-select-sm">
                                <option value="">Wszystkie</option>
                                @foreach($kategorie as $kat)
                                    <option value="{{ $kat }}" {{ request('kategoria') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Zakres dat</label>
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text">Od</span>
                                <input type="date" name="data_od" class="form-control"
                                       value="{{ $dzienOd->toDateString() }}" min="{{ now()->toDateString() }}">
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Do</span>
                                <input type="date" name="data_do" class="form-control"
                                       value="{{ $dzienDo->toDateString() }}" min="{{ now()->toDateString() }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cena (zł)</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="cena_min" min="0" class="form-control form-control-sm"
                                       value="{{ request('cena_min') }}" placeholder="od">
                                <input type="number" name="cena_max" min="0" class="form-control form-control-sm"
                                       value="{{ request('cena_max') }}" placeholder="do">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Godziny wizyty</label>
                            <div class="d-flex gap-2">
                                <input type="time" name="godzina_od" class="form-control form-control-sm"
                                       value="{{ request('godzina_od') }}">
                                <input type="time" name="godzina_do" class="form-control form-control-sm"
                                       value="{{ request('godzina_do') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimalna ocena</label>
                            <select name="ocena_min" class="form-select form-select-sm">
                                <option value="">Dowolna</option>
                                @foreach([4.5, 4.0, 3.5, 3.0] as $oc)
                                    <option value="{{ $oc }}" {{ request('ocena_min') == $oc ? 'selected' : '' }}>
                                        {{ number_format($oc, 1) }} i więcej
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'trafnosc') }}">
                        <input type="hidden" name="sw_lat" id="sw_lat" value="{{ request('sw_lat') }}">
                        <input type="hidden" name="sw_lon" id="sw_lon" value="{{ request('sw_lon') }}">
                        <input type="hidden" name="ne_lat" id="ne_lat" value="{{ request('ne_lat') }}">
                        <input type="hidden" name="ne_lon" id="ne_lon" value="{{ request('ne_lon') }}">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i> Szukaj
                            </button>
                            <a href="{{ route('szukaj') }}" class="btn btn-outline-secondary btn-sm">Wyczyść filtry</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-map"></i> Mapa</h6>
                </div>
                <div class="card-body p-2">
                    <div id="map" style="height: 320px; border-radius: 6px; z-index: 1;"></div>
                    <button type="button" id="szukajTuBtn" class="btn btn-outline-primary btn-sm w-100 mt-2">
                        <i class="bi bi-arrow-repeat"></i> Szukaj w tym obszarze
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-9">

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <span class="text-muted">
                    Znaleziono <strong>{{ $services->total() }}</strong> usług
                    @if(request('usluga'))
                        dla „{{ request('usluga') }}"
                    @endif
                </span>

                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Sortuj:</label>
                    <select class="form-select form-select-sm w-auto" id="sortSelect">
                        <option value="trafnosc"      {{ request('sort', 'trafnosc') === 'trafnosc' ? 'selected' : '' }}>Trafność</option>
                        <option value="popularnosc"   {{ request('sort') === 'popularnosc' ? 'selected' : '' }}>Popularność</option>
                        <option value="ocena"         {{ request('sort') === 'ocena' ? 'selected' : '' }}>Najwyżej oceniane</option>
                        <option value="cena_rosnaco"  {{ request('sort') === 'cena_rosnaco' ? 'selected' : '' }}>Cena: od najniższej</option>
                        <option value="cena_malejaco" {{ request('sort') === 'cena_malejaco' ? 'selected' : '' }}>Cena: od najwyższej</option>
                    </select>
                </div>
            </div>

            @forelse($services as $service)
                @include('search.partials.result-card', ['service' => $service])
            @empty
                <div class="card shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-emoji-frown fs-1"></i>
                        <p class="mt-3 mb-0">Brak usług spełniających kryteria. Spróbuj poluzować filtry.</p>
                    </div>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sortSelect = document.getElementById('sortSelect');
        var sortInput = document.getElementById('sortInput');
        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                sortInput.value = this.value;
                document.getElementById('filtryForm').submit();
            });
        }

        if (typeof L === 'undefined') {
            document.getElementById('map').innerHTML = "<p class='text-danger text-center pt-5'>Nie udało się załadować mapy.</p>";
            return;
        }

        var pins = @json($pins);

        var map = L.map('map').setView([52.069, 19.254], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var markers = [];
        pins.forEach(function (p) {
            var m = L.marker([p.lat, p.lon]).addTo(map);
            m.bindPopup('<strong>' + p.name + '</strong><br><a href="' + p.url + '">Zobacz lokal</a>');
            markers.push(m);
        });

        if (markers.length > 0) {
            var group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.2));
        }

        document.getElementById('szukajTuBtn').addEventListener('click', function () {
            var b = map.getBounds();
            document.getElementById('sw_lat').value = b.getSouth().toFixed(7);
            document.getElementById('sw_lon').value = b.getWest().toFixed(7);
            document.getElementById('ne_lat').value = b.getNorth().toFixed(7);
            document.getElementById('ne_lon').value = b.getEast().toFixed(7);
            document.getElementById('filtryForm').submit();
        });
    });
</script>
@endsection
