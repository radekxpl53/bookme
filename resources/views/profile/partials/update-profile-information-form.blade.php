<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">Dane konta</h6>
    </div>
    <div class="card-body">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success py-2">Zapisano zmiany.</div>
        @endif

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="username" class="form-label">Nazwa użytkownika *</label>
                <input type="text" id="username" name="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', $user->username) }}" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Imię *</label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="surname" class="form-label">Nazwisko *</label>
                    <input type="text" id="surname" name="surname"
                           class="form-control @error('surname') is-invalid @enderror"
                           value="{{ old('surname', $user->surname) }}" required>
                    @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telefon *</label>
                <input type="text" inputmode="tel" id="phone" name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $user->phone) }}" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-success">Zapisz zmiany</button>
        </form>
    </div>
</div>
