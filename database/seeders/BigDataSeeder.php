<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessPhoto;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkingHour;
use App\Models\Appointment;
use App\Models\BusinessReview;
use App\Models\EmployeeReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BigDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Rozpoczynam generowanie potężnej bazy danych...');

        // 1. Klienci (50 osób)
        $this->command->info('Generowanie 50 klientów...');
        $clients = User::factory(50)->create([
            'is_admin' => false,
        ]);

        // 2. Właściciele biznesów (10 osób)
        $this->command->info('Generowanie 10 właścicieli biznesów...');
        $owners = User::factory(10)->create([
            'is_admin' => false,
        ]);

        $categories = ['Fryzjer', 'Barber', 'Kosmetyczka', 'Masaż', 'Paznokcie', 'Brwi i rzęsy'];
        $komentarze = [
            'Niesamowita atmosfera i pełen profesjonalizm.',
            'Zawsze wychodzę zadowolony.',
            'Bardzo czysto i estetycznie.',
            'Najlepsi w mieście, bez dwóch zdań!',
            'Polecam z czystym sumieniem każdemu.',
            'Ceny adekwatne do jakości.',
            'Nie zamienię ich na nikogo innego!',
            'Trochę długo czekałem, ale efekt super.',
            'Mają świetne podejście do klienta.',
        ];

        // 3. Biznesy (około 40)
        $this->command->info('Generowanie 40 lokali z usługami i pracownikami...');
        foreach ($owners as $owner) {
            $businessCount = rand(2, 5);
            
            for ($b = 0; $b < $businessCount; $b++) {
                $business = Business::create([
                    'owner_id' => $owner->id,
                    'name' => fake()->company() . ' ' . fake()->randomElement(['Studio', 'Salon', 'Spa', 'Clinic']),
                    'category' => fake()->randomElement($categories),
                    'address' => fake()->address(),
                    'description' => fake()->realText(200),
                    'lat' => fake()->latitude(50, 54),
                    'lon' => fake()->longitude(15, 23),
                    'is_approved' => true,
                ]);

                // Usługi dla biznesu (5-10)
                $services = [];
                $serviceCount = rand(5, 10);
                for ($s = 0; $s < $serviceCount; $s++) {
                    $services[] = Service::create([
                        'business_id' => $business->id,
                        'name' => 'Usługa ' . fake()->word() . ' ' . Str::random(3),
                        'price' => fake()->randomFloat(2, 50, 300),
                        'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90, 120]),
                    ]);
                }
                $servicesCollection = collect($services);

                // Pracownicy dla biznesu (3-8)
                $employeeCount = rand(3, 8);
                $employees = [];
                for ($e = 0; $e < $employeeCount; $e++) {
                    $emp = Employee::create([
                        'business_id' => $business->id,
                        'name' => fake()->firstName() . ' ' . fake()->lastName(),
                        'specialization' => fake()->jobTitle(),
                        'is_active' => true,
                    ]);
                    
                    // Przypisanie 2-4 losowych usług
                    $emp->services()->sync(
                        $servicesCollection->random(rand(2, 4))->pluck('id')->toArray()
                    );
                    
                    // Grafik pracy
                    foreach (range(1, 5) as $day) {
                        WorkingHour::create([
                            'employee_id' => $emp->id,
                            'day_of_week' => $day,
                            'start_time' => '08:00',
                            'end_time' => '16:00',
                        ]);
                    }
                    $employees[] = $emp;
                }

                // Generowanie Wizyt i Opinii
                foreach ($employees as $employee) {
                    $empServices = $employee->services;
                    if ($empServices->isEmpty()) continue;

                    // 10-20 historycznych i przyszłych wizyt dla pracownika
                    $apptCount = rand(10, 20);
                    for ($a = 0; $a < $apptCount; $a++) {
                        $service = $empServices->random();
                        $client = $clients->random();
                        
                        $daysOffset = rand(-30, 14); // od miesiąca wstecz do 2 tyg w przód
                        $start = Carbon::today()->addDays($daysOffset)->setTimeFromTimeString(fake()->randomElement(['09:00', '10:00', '11:00', '13:00', '14:00', '15:00']));
                        
                        $status = 'pending';
                        if ($start->isPast()) {
                            $status = fake()->randomElement(['completed', 'completed', 'completed', 'cancelled']);
                        } else {
                            $status = fake()->randomElement(['confirmed', 'pending']);
                        }

                        Appointment::create([
                            'client_id' => $client->id,
                            'employee_id' => $employee->id,
                            'service_id' => $service->id,
                            'start_at' => $start,
                            'finish_at' => $start->copy()->addMinutes($service->duration_minutes),
                            'status' => $status,
                            'total_price' => $service->price,
                        ]);

                        // Jeśli zrealizowana, jest 50% szans na opinię o pracowniku
                        if ($status === 'completed' && rand(1, 100) > 50) {
                            EmployeeReview::create([
                                'employee_id' => $employee->id,
                                'user_id' => $client->id,
                                'service' => $service->name,
                                'rating' => rand(4, 5),
                                'comment' => fake()->randomElement($komentarze),
                                'created_at' => $start->copy()->addDays(1)
                            ]);
                        }
                    }
                }

                // Opinie o samym biznesie (5-15)
                $bizReviewCount = rand(5, 15);
                for ($r = 0; $r < $bizReviewCount; $r++) {
                    BusinessReview::create([
                        'business_id' => $business->id,
                        'user_id' => $clients->random()->id,
                        'rating' => fake()->randomElement([4, 4, 5, 5, 5, 3]),
                        'comment' => fake()->randomElement($komentarze),
                        'created_at' => Carbon::today()->subDays(rand(1, 60))
                    ]);
                }
            }
        }

        $this->command->info('Baza została wypełniona ogromną ilością danych!');
    }
}
