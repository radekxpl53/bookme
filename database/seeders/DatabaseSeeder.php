<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bookme.test'],
            [
                'username' => 'admin',
                'first_name' => 'Szef',
                'surname' => 'Wszystkich Szefow',
                'phone' => '000111888',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'wlasciciel@bookme.test'],
            [
                'username' => '6januszexpl7',
                'first_name' => 'Janusz',
                'surname' => 'Biznesu',
                'phone' => '312532980',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'klient@bookme.test'],
            [
                'username' => 'kamil69',
                'first_name' => 'Kamil',
                'surname' => 'Kowalski',
                'phone' => '584015739',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        $owner = User::where('email', 'wlasciciel@bookme.test')->first();

        if ($owner->businesses()->count() === 0) {
            $businesses = \App\Models\Business::factory(5)->create([
                'owner_id' => $owner->id,
            ]);

            foreach ($businesses as $business) {
                \App\Models\Employee::factory(3)->create([
                    'business_id' => $business->id,
                ]);

                \App\Models\Service::factory(4)->create([
                    'business_id' => $business->id,
                ]);
            }
        }
    }
}
