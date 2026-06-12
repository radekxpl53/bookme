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
            ['email' => 'admin@admin.com'],
            [
                'username' => 'admin',
                'first_name' => 'Szef',
                'surname' => 'Wszystkich Szefow',
                'phone' => '000111888',
                'password' => Hash::make('admin'),
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
        $client = User::where('email', 'klient@bookme.test')->first();

        if ($owner->businesses()->count() === 0) {
            $recenzenci = User::factory(8)->create();
            $komentarze = [
                'Bardzo polecam, obsługa na medal!',
                'Profesjonalnie i miło, wrócę na pewno.',
                'Czysto, sprawnie i w dobrej cenie.',
                'Świetny efekt, jestem bardzo zadowolony.',
                'Mili ludzie, terminowo i solidnie.',
            ];

            $businesses = \App\Models\Business::factory(5)->create([
                'owner_id' => $owner->id,
                'is_approved' => true,
            ]);

            foreach ($businesses as $business) {
                $employees = \App\Models\Employee::factory(3)->create([
                    'business_id' => $business->id,
                ]);

                $services = \App\Models\Service::factory(4)->create([
                    'business_id' => $business->id,
                ]);

                foreach ($employees as $employee) {
                    $employee->services()->sync(
                        $services->random(rand(2, 3))->pluck('id')->toArray()
                    );

                    foreach (range(1, 5) as $dzien) {
                        \App\Models\WorkingHour::create([
                            'employee_id' => $employee->id,
                            'day_of_week' => $dzien,
                            'start_time' => '09:00',
                            'end_time' => '17:00',
                        ]);
                    }
                }

                $employee = $employees->first();
                $service = $employee->services->first();
                if ($service) {
                    foreach (['10:00', '13:00'] as $godzina) {
                        $start = \Carbon\Carbon::today()->setTimeFromTimeString($godzina);
                        \App\Models\Appointment::create([
                            'client_id' => $client->id,
                            'employee_id' => $employee->id,
                            'service_id' => $service->id,
                            'start_at' => $start,
                            'finish_at' => $start->copy()->addMinutes($service->duration_minutes),
                            'status' => 'confirmed',
                            'total_price' => $service->price,
                        ]);
                    }
                }

                foreach ($recenzenci->random(rand(3, 5)) as $recenzent) {
                    \App\Models\BusinessReview::create([
                        'business_id' => $business->id,
                        'user_id' => $recenzent->id,
                        'rating' => rand(3, 5),
                        'comment' => fake()->randomElement($komentarze),
                    ]);
                }

                foreach ($employees as $employee) {
                    foreach ($recenzenci->random(rand(1, 3)) as $recenzent) {
                        \App\Models\EmployeeReview::create([
                            'employee_id' => $employee->id,
                            'user_id' => $recenzent->id,
                            'service' => $employee->services->first()?->name,
                            'rating' => rand(3, 5),
                            'comment' => fake()->randomElement($komentarze),
                        ]);
                    }
                }
            }
        }
    }
}
