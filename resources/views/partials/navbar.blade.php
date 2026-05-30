<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">✂️ BookMe</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Przełącz nawigację">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Strona główna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Szukaj usługi</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    @if(Auth::user()->isOwner())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('biznes.lokale.index') }}">
                                <i class="bi bi-shop"></i> Moje lokale
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <span class="nav-link text-white">Witaj, {{ Auth::user()->first_name }}!</span>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="#">Zaloguj się</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                        <a class="btn btn-primary btn-sm" href="#">Załóż konto</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
