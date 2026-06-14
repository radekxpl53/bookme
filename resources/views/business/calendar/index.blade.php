@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold"><i class="bi bi-calendar-week text-primary me-2"></i>Kalendarz Wizyt</h2>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('biznes.lokale.index') }}">Moje lokale</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $business->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="card shadow-sm border-0 bg-white px-3 py-2">
            <div class="d-flex align-items-center">
                <i class="bi bi-funnel text-muted me-2"></i>
                <select id="employee-filter" class="form-select border-0 bg-transparent fw-bold shadow-none" style="outline: none;">
                    <option value="">Wszyscy pracownicy</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>



    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4 bg-white">
            <div id="calendar"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="status-form" method="POST" action="">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="appointmentModalLabel">Szczegóły Wizyty</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <strong>Klient:</strong> <span id="modal-client"></span> <br>
                <strong>Telefon:</strong> <span id="modal-phone"></span>
            </div>
            <div class="mb-3">
                <strong>Usługa:</strong> <span id="modal-service"></span> <br>
                <strong>Pracownik:</strong> <span id="modal-employee"></span> <br>
                <strong>Cena:</strong> <span id="modal-price"></span> zł
            </div>
            <div class="mb-3">
                <label for="status-select" class="form-label fw-bold">Zmień status wizyty:</label>
                <select name="status" id="status-select" class="form-select">
                    <option value="pending">Oczekująca (Pending)</option>
                    <option value="confirmed">Zatwierdzona (Confirmed)</option>
                    <option value="completed">Zakończona (Completed)</option>
                    <option value="cancelled">Anulowana (Cancelled)</option>
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            <button type="submit" class="btn btn-primary">Zapisz status</button>
          </div>
      </form>
    </div>
  </div>
</div>


<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var filterEl = document.getElementById('employee-filter');
        var modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'pl',
            firstDay: 1,
            height: 'auto',
            buttonText: {
                today: 'Dzisiaj',
                month: 'Miesiąc',
                week: 'Tydzień',
                day: 'Dzień',
                list: 'Lista'
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
                
                url += '?start=' + info.startStr + '&end=' + info.endStr;
                if(employeeId) {
                    url += '&employee_id=' + employeeId;
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
