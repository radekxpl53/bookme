#!/bin/bash

# Skopiuj plik .env, jesli go nie ma
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Zainstaluj zaleznosci, jesli brakuje folderu vendor
if [ ! -d vendor ]; then
    composer install
fi

# Wygeneruj klucz aplikacji
php artisan key:generate

# Poczekaj chwile, az baza danych wstanie
sleep 5

# Uruchom migracje
php artisan migrate --force

# Uruchom serwer
php artisan serve --host=0.0.0.0 --port=8000
