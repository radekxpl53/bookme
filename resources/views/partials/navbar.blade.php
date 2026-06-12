<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">BookMe</a>
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
                    <a class="nav-link" href="{{ route('szukaj') }}">Szukaj usługi</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-shield-lock-fill"></i> Panel Admina
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->isOwner())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('biznes.lokale.index') }}">
                                <i class="bi bi-shop"></i> Moje lokale
                            </a>
                        </li>
                    @elseif(!Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link text-success fw-bold" href="{{ route('biznes.lokale.create') }}">
                                <i class="bi bi-plus-circle"></i> Dodaj swój salon
                            </a>
                        </li>
                    @endif
                    @if(!Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('klient.wizyty.index') }}">
                                <i class="bi bi-calendar-check"></i> Moje wizyty
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <span class="nav-link text-white">Witaj, {{ Auth::user()->first_name }}!</span>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Wyloguj</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Zaloguj się</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Załóż konto</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
