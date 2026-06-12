@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Kalendarz Wizyt</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-2 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.index') }}">Moje lokale</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $business->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <select id="employee-filter" class="form-select w-auto">
                <option value="">Wszyscy pracownicy</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow border-0 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check text-primary me-2"></i> Podgląd harmonogramu</h5>
        </div>
        <div class="card-body p-4 bg-light">
            <div id="calendar" class="bg-white p-3 rounded shadow-sm border"></div>
        </div>
    </div>
</div>

<!-- Modal Szczegółów Wizyty -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form id="status-form" method="POST" action="">
          @csrf
          <div class="modal-header bg-light border-bottom-0">
            <h5 class="modal-title fw-bold" id="appointmentModalLabel"><i class="bi bi-info-circle text-primary me-2"></i>Szczegóły Wizyty</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
          </div>
          <div class="modal-body py-4">
            <div class="row mb-3">
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold">Klient</span><br>
                    <span id="modal-client" class="fs-5"></span>
                </div>
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold">Telefon</span><br>
                    <span id="modal-phone" class="fs-5"></span>
                </div>
            </div>
            <hr class="text-muted">
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <span class="text-muted small text-uppercase fw-bold">Usługa</span><br>
                    <span id="modal-service" class="fs-5 fw-bold text-primary"></span>
                </div>
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold">Pracownik</span><br>
                    <span id="modal-employee"></span>
                </div>
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold">Cena</span><br>
                    <span class="fw-bold text-success"><span id="modal-price"></span> zł</span>
                </div>
            </div>
            <div class="bg-light p-3 rounded border">
                <label for="status-select" class="form-label fw-bold mb-2">Zmień status wizyty:</label>
                <select name="status" id="status-select" class="form-select border-secondary">
                    <option value="pending">⏳ Oczekująca</option>
                    <option value="confirmed">✅ Zatwierdzona</option>
                    <option value="completed">🎉 Zakończona</option>
                    <option value="cancelled">❌ Anulowana</option>
                </select>
            </div>
          </div>
          <div class="modal-footer bg-light border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            <button type="submit" class="btn btn-primary fw-bold px-4">Zapisz status</button>
          </div>
      </form>
    </div>
  </div>
</div>

<style>
    /* Niewielkie poprawki dla FullCalendar */
    .fc-event { cursor: pointer; padding: 2px 4px; border-radius: 4px; border: none !important; }
    .fc-event-title { font-weight: 600; }
    .fc-theme-bootstrap5 .fc-scrollgrid { border-color: #dee2e6; }
</style>

<!-- FullCalendar JS & CSS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.10/index.global.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var filterEl = document.getElementById('employee-filter');
        var modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'timeGridWeek',
            locale: 'pl',
            height: 'auto',
            expandRows: true,
            firstDay: 1,
            buttonText: {
                today:    'Dziś',
                month:    'Miesiąc',
                week:     'Tydzień',
                day:      'Dzień',
                list:     'Lista'
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotMinTime: '06:00:00',
            slotMaxTime: '22:00:00',
            allDaySlot: false,
            events: function(info, successCallback, failureCallback) {
                var employeeId = filterEl.value;
                var url = "{{ route('biznes.lokale.kalendarz.events', $business) }}";
                
                url += '?start=' + encodeURIComponent(info.startStr) + '&end=' + encodeURIComponent(info.endStr);
                if(employeeId) {
                    url += '&employee_id=' + encodeURIComponent(employeeId);
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            eventClick: function(info) {
                var props = info.event.extendedProps;
                
                document.getElementById('modal-client').textContent = props.client_name;
                document.getElementById('modal-phone').textContent = props.client_phone;
                document.getElementById('modal-service').textContent = props.service_name;
                document.getElementById('modal-employee').textContent = props.employee_name;
                document.getElementById('modal-price').textContent = props.price;
                document.getElementById('status-select').value = props.status;

                var form = document.getElementById('status-form');
                form.action = "{{ route('biznes.lokale.kalendarz.status', ['business' => $business->id, 'appointment' => 'ID_PLACEHOLDER']) }}".replace('ID_PLACEHOLDER', info.event.id);
                
                modal.show();
            }
        });

        calendar.render();

        filterEl.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
</script>
@endsection
