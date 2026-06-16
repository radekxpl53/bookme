<div class="card shadow-sm mb-4 border-danger">
    <div class="card-header bg-danger text-white">
        <h6 class="mb-0">Usuń konto</h6>
    </div>
    <div class="card-body">
        <p class="text-muted small">
            Po usunięciu konta wszystkie Twoje dane zostaną trwale skasowane. Tej operacji nie można cofnąć.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Na pewno usunąć konto? Tej operacji nie można cofnąć.');">
            @csrf
            @method('delete')

            <div class="mb-3">
                <label for="password_delete" class="form-label">Potwierdź hasłem</label>
                <input type="password" id="password_delete" name="password"
                       class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                       autocomplete="current-password">
                @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-danger">Usuń konto</button>
        </form>
    </div>
</div>
