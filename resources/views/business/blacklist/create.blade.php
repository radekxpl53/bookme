@extends('layouts.app')

@section('title', 'Zablokuj klienta — ' . $business->name)

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 border-top border-dark border-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Zablokuj klienta — {{ $business->name }}</h5>
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

                    <form action="{{ route('biznes.lokale.blacklist.store', $business) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Adres e-mail klienta *</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="jan@example.com">
                            <div class="form-text">Klient zostanie zablokowany i nie będzie mógł rezerwować wizyt w Twoim lokalu.</div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Powód blokady (opcjonalnie)</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Np. nie przyszedł na wizytę 3 razy z rzędu">{{ old('reason') }}</textarea>
                            <div class="form-text">Powód jest widoczny tylko dla Ciebie i innych pracowników lokalu.</div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('biznes.lokale.blacklist.index', $business) }}" class="btn btn-secondary">Anuluj</a>
                            <button type="submit" class="btn btn-dark">Zablokuj klienta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
