<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">Zmiana hasła</h6>
    </div>
    <div class="card-body">
        @if (session('status') === 'password-updated')
            <div class="alert alert-success py-2">Hasło zostało zmienione.</div>
        @endif

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="current_password" class="form-label">Obecne hasło *</label>
                <input type="password" id="current_password" name="current_password"
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                       autocomplete="current-password" required>
                @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nowe hasło *</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                       autocomplete="new-password" required>
                @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Powtórz nowe hasło *</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-success">Zmień hasło</button>
        </form>
    </div>
</div>
