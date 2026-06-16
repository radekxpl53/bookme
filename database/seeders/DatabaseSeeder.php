<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessPhoto;
use App\Models\BusinessReview;
use App\Models\Employee;
use App\Models\EmployeePortfolio;
use App\Models\EmployeeReview;
use App\Models\Image;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private int $imageSeed = 1000;

    private array $comments = [
        'Bardzo polecam, obsługa na medal!',
        'Profesjonalnie i miło, wrócę na pewno.',
        'Czysto, sprawnie i w dobrej cenie.',
        'Świetny efekt, jestem bardzo zadowolony.',
        'Mili ludzie, terminowo i solidnie.',
        'Super atmosfera, dokładnie tak jak chciałam.',
        'Trochę czekałem, ale efekt wynagrodził.',
        'Najlepsze miejsce w okolicy, obsługa pierwsza klasa.',
    ];

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            ['username' => 'admin', 'first_name' => 'Adam', 'surname' => 'Administrator', 'phone' => '500100100', 'password' => Hash::make('admin'), 'is_admin' => true]
        );

        $owner1 = User::updateOrCreate(
            ['email' => 'wlasciciel@bookme.test'],
            ['username' => 'wlasciciel', 'first_name' => 'Janusz', 'surname' => 'Biznesowy', 'phone' => '600200300', 'password' => Hash::make('password'), 'is_admin' => false]
        );

        $owner2 = User::updateOrCreate(
            ['email' => 'wlasciciel2@bookme.test'],
            ['username' => 'wlasciciel2', 'first_name' => 'Marta', 'surname' => 'Nowicka', 'phone' => '600200301', 'password' => Hash::make('password'), 'is_admin' => false]
        );

        $owner3 = User::updateOrCreate(
            ['email' => 'wlasciciel3@bookme.test'],
            ['username' => 'wlasciciel3', 'first_name' => 'Robert', 'surname' => 'Kowalczyk', 'phone' => '600200302', 'password' => Hash::make('password'), 'is_admin' => false]
        );

        $client = User::updateOrCreate(
            ['email' => 'klient@bookme.test'],
            ['username' => 'klient', 'first_name' => 'Kamil', 'surname' => 'Kowalski', 'phone' => '700300400', 'password' => Hash::make('password'), 'is_admin' => false]
        );

        if ($owner1->businesses()->count() > 0) {
            return;
        }

        $reviewers = $this->createReviewers();
        $clients = $reviewers->concat([$client]);

        foreach ($this->salons() as $index => $salon) {
            $owner = $index < 4 ? $owner1 : $owner2;
            $this->seedApprovedBusiness($salon, $owner, $clients);
        }

        foreach ($this->pendingBusinesses() as $i => $pending) {
            $this->seedPendingBusiness($pending, $i === 0 ? $owner2 : $owner3);
        }

        $this->seedClientAppointments($client);
    }

    private function seedApprovedBusiness(array $salon, User $owner, $clients): void
    {
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => $salon['name'],
            'category' => $salon['category'],
            'address' => $salon['address'],
            'lat' => $salon['lat'],
            'lon' => $salon['lon'],
            'description' => $salon['description'],
            'is_approved' => true,
        ]);

        foreach (range(1, 2) as $i) {
            $path = $this->fetchImage('business_photos');
            if ($path) {
                BusinessPhoto::create(['business_id' => $business->id, 'path' => $path]);
            }
        }

        $services = collect($salon['services'])->map(fn ($s) => Service::create([
            'business_id' => $business->id,
            'name' => $s[0],
            'price' => $s[1],
            'duration_minutes' => $s[2],
        ]));

        $employees = collect($salon['employees'])->map(function ($name) use ($business, $salon, $services) {
            $employee = Employee::create([
                'business_id' => $business->id,
                'name' => $name,
                'specialization' => $salon['specialization'],
                'is_active' => true,
            ]);

            $employee->services()->sync($services->pluck('id')->toArray());
            $this->seedWorkingHours($employee);

            $path = $this->fetchImage('employee_portfolio');
            if ($path) {
                EmployeePortfolio::create(['employee_id' => $employee->id, 'path' => $path, 'description' => 'Przykład pracy: '.$salon['specialization']]);
            }

            return $employee;
        });

        $this->seedReviews($business, $employees, $services, $clients);
        $this->seedAppointments($business, $employees, $services, $clients);
    }

    private function seedPendingBusiness(array $data, User $owner): void
    {
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => $data['name'],
            'category' => $data['category'],
            'address' => $data['address'],
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'description' => $data['description'],
            'is_approved' => false,
        ]);

        $services = collect($data['services'])->map(fn ($s) => Service::create([
            'business_id' => $business->id,
            'name' => $s[0],
            'price' => $s[1],
            'duration_minutes' => $s[2],
        ]));

        $employee = Employee::create([
            'business_id' => $business->id,
            'name' => $data['employee'],
            'specialization' => $data['category'],
            'is_active' => true,
        ]);

        $employee->services()->sync($services->pluck('id')->toArray());
        $this->seedWorkingHours($employee);
    }

    private function seedWorkingHours(Employee $employee): void
    {
        foreach (range(1, 5) as $day) {
            WorkingHour::create(['employee_id' => $employee->id, 'day_of_week' => $day, 'start_time' => '09:00', 'end_time' => '18:00']);
        }
        WorkingHour::create(['employee_id' => $employee->id, 'day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '14:00']);
    }

    private function seedReviews(Business $business, $employees, $services, $clients): void
    {
        foreach ($clients->random(rand(4, 7))->values() as $j => $reviewer) {
            BusinessReview::create([
                'business_id' => $business->id,
                'user_id' => $reviewer->id,
                'rating' => rand(3, 5),
                'comment' => fake()->randomElement($this->comments),
            ]);

            if ($j === 0) {
                $path = $this->fetchImage('review_images');
                if ($path) {
                    $image = Image::create(['file_name' => $path, 'original_file_name' => 'opinia.jpg', 'file_type' => 'image/jpeg']);
                    $business->reviewImages()->attach($image->id, ['user_id' => $reviewer->id]);
                }
            }
        }

        foreach ($employees as $employee) {
            foreach ($clients->random(rand(2, 4)) as $reviewer) {
                EmployeeReview::create([
                    'employee_id' => $employee->id,
                    'user_id' => $reviewer->id,
                    'service' => $services->random()->name,
                    'rating' => rand(3, 5),
                    'comment' => fake()->randomElement($this->comments),
                ]);
            }
        }
    }

    private function seedAppointments(Business $business, $employees, $services, $clients): void
    {
        foreach (range(1, rand(5, 8)) as $i) {
            $this->makeAppointment($employees->random(), $services->random(), $clients->random(), Carbon::today()->subDays(rand(2, 45)), 'completed');
        }

        foreach (range(1, rand(3, 6)) as $i) {
            $this->makeAppointment($employees->random(), $services->random(), $clients->random(), Carbon::today()->addDays(rand(1, 14)), fake()->randomElement(['confirmed', 'pending']));
        }

        foreach (range(1, rand(1, 3)) as $i) {
            $this->makeAppointment($employees->random(), $services->random(), $clients->random(), Carbon::today()->subDays(rand(1, 20)), 'cancelled');
        }
    }

    private function seedClientAppointments(User $client): void
    {
        $business = Business::where('is_approved', true)->with(['employees', 'services'])->first();
        if (! $business) {
            return;
        }

        $employee = $business->employees->first();
        $service = $business->services->first();

        $this->makeAppointment($employee, $service, $client, Carbon::today()->subDays(7), 'completed', '11:00');
        $this->makeAppointment($employee, $service, $client, Carbon::today()->addDays(2), 'confirmed', '14:00');
        $this->makeAppointment($employee, $service, $client, Carbon::today()->subDays(3), 'cancelled', '12:00');
    }

    private function makeAppointment(Employee $employee, Service $service, User $client, Carbon $day, string $status, ?string $time = null): void
    {
        $start = $time
            ? $day->copy()->setTimeFromTimeString($time)
            : $day->copy()->setTime(rand(9, 16), [0, 15, 30, 45][rand(0, 3)]);

        Appointment::create([
            'client_id' => $client->id,
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'start_at' => $start,
            'finish_at' => $start->copy()->addMinutes($service->duration_minutes),
            'status' => $status,
            'total_price' => $service->price,
        ]);
    }

    private function fetchImage(string $folder): ?string
    {
        $seed = $this->imageSeed++;

        try {
            $response = Http::timeout(20)->get("https://picsum.photos/seed/{$folder}{$seed}/600/400");
            if ($response->successful() && strlen($response->body()) > 1000) {
                $name = $folder.'/'.Str::random(24).'.jpg';
                Storage::disk('public')->put($name, $response->body());

                return $name;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    private function createReviewers()
    {
        $people = [
            ['Anna', 'Nowak'], ['Piotr', 'Wisniewski'], ['Magdalena', 'Wojcik'], ['Tomasz', 'Kaminski'],
            ['Katarzyna', 'Lewandowska'], ['Marcin', 'Zielinski'], ['Agnieszka', 'Szymanska'], ['Pawel', 'Dabrowski'],
            ['Joanna', 'Kozlowska'], ['Michal', 'Jankowski'], ['Aleksandra', 'Mazur'], ['Krzysztof', 'Wojciechowski'],
            ['Natalia', 'Krawczyk'], ['Grzegorz', 'Kaczmarek'], ['Monika', 'Piotrowska'], ['Adam', 'Grabowski'],
            ['Karolina', 'Pawlowska'], ['Jakub', 'Michalski'], ['Ewa', 'Nowakowska'], ['Rafal', 'Adamczyk'],
        ];

        return collect($people)->map(fn ($p, $i) => User::create([
            'username' => Str::lower($p[0]).'_'.$i,
            'first_name' => $p[0],
            'surname' => $p[1],
            'phone' => '5'.str_pad((string) (10000000 + $i), 8, '0', STR_PAD_LEFT),
            'email' => Str::lower($p[0]).$i.'@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]));
    }

    private function pendingBusinesses(): array
    {
        return [
            [
                'name' => 'Nowy Salon Glamour',
                'category' => 'Fryzjer',
                'address' => 'ul. Wojska Polskiego 14, 70-470 Szczecin',
                'lat' => 53.4289, 'lon' => 14.5530,
                'description' => 'Świeżo otwarty salon fryzjerski — czeka na weryfikację.',
                'employee' => 'Beata Sosnowska',
                'services' => [
                    ['Strzyżenie damskie', 75, 60],
                    ['Koloryzacja', 170, 120],
                ],
            ],
            [
                'name' => 'Barber Studio Premium',
                'category' => 'Barber',
                'address' => 'ul. Gdańska 50, 85-006 Bydgoszcz',
                'lat' => 53.1235, 'lon' => 18.0084,
                'description' => 'Nowy barber shop zgłoszony do weryfikacji.',
                'employee' => 'Kacper Wróblewski',
                'services' => [
                    ['Strzyżenie męskie', 55, 45],
                    ['Strzyżenie + broda', 75, 60],
                ],
            ],
        ];
    }

    private function salons(): array
    {
        return [
            [
                'name' => 'Strzyżka Barber Shop', 'category' => 'Barber',
                'address' => 'ul. Marszałkowska 10, 00-590 Warszawa', 'lat' => 52.2225, 'lon' => 21.0125,
                'description' => 'Klasyczny barber shop w sercu Warszawy. Strzyżenie, broda i golenie brzytwą.',
                'specialization' => 'Barber',
                'services' => [['Strzyżenie męskie', 50, 45], ['Strzyżenie brody', 30, 30], ['Strzyżenie + broda', 70, 60], ['Golenie brzytwą', 40, 30]],
                'employees' => ['Marek Adamczyk', 'Łukasz Krawczyk', 'Bartosz Mazur'],
            ],
            [
                'name' => 'Salon Fryzjerski Elegance', 'category' => 'Fryzjer',
                'address' => 'ul. Floriańska 25, 31-019 Kraków', 'lat' => 50.0625, 'lon' => 19.9404,
                'description' => 'Salon fryzjerski dla kobiet i mężczyzn. Strzyżenie, koloryzacja, stylizacja.',
                'specialization' => 'Fryzjer',
                'services' => [['Strzyżenie damskie', 80, 60], ['Modelowanie', 60, 45], ['Koloryzacja', 180, 120], ['Strzyżenie męskie', 50, 30]],
                'employees' => ['Joanna Pawlak', 'Natalia Górska', 'Sylwia Witkowska'],
            ],
            [
                'name' => 'Studio Paznokcia Nails & Co', 'category' => 'Paznokcie',
                'address' => 'ul. Świdnicka 12, 50-068 Wrocław', 'lat' => 51.1067, 'lon' => 17.0319,
                'description' => 'Manicure, pedicure i przedłużanie paznokci. Trwałe i estetyczne wykończenie.',
                'specialization' => 'Stylistka paznokci',
                'services' => [['Manicure hybrydowy', 90, 60], ['Pedicure', 110, 75], ['Przedłużanie paznokci', 150, 120], ['Manicure klasyczny', 60, 45]],
                'employees' => ['Karolina Sikora', 'Patrycja Baran'],
            ],
            [
                'name' => 'Gabinet Kosmetyczny Beauty Care', 'category' => 'Kosmetyczka',
                'address' => 'ul. Półwiejska 5, 61-888 Poznań', 'lat' => 52.4035, 'lon' => 16.9290,
                'description' => 'Zabiegi pielęgnacyjne na twarz i ciało. Oczyszczanie, peelingi, makijaż.',
                'specialization' => 'Kosmetolog',
                'services' => [['Oczyszczanie twarzy', 150, 75], ['Peeling kawitacyjny', 120, 60], ['Makijaż okolicznościowy', 200, 90], ['Zabieg nawilżający', 130, 60]],
                'employees' => ['Monika Krupa', 'Aleksandra Duda'],
            ],
            [
                'name' => 'Strefa Relaksu Masaż & Spa', 'category' => 'Masaż',
                'address' => 'ul. Długa 40, 80-827 Gdańsk', 'lat' => 54.3489, 'lon' => 18.6533,
                'description' => 'Masaże relaksacyjne, lecznicze i sportowe w spokojnej atmosferze spa.',
                'specialization' => 'Masażysta',
                'services' => [['Masaż relaksacyjny', 140, 60], ['Masaż leczniczy', 160, 60], ['Masaż gorącymi kamieniami', 180, 75], ['Masaż sportowy', 150, 60]],
                'employees' => ['Robert Wieczorek', 'Damian Sobczak', 'Ewa Zawadzka'],
            ],
            [
                'name' => 'Studio Lash — Brwi i Rzęsy', 'category' => 'Brwi i rzęsy',
                'address' => 'ul. Piotrkowska 100, 90-004 Łódź', 'lat' => 51.7687, 'lon' => 19.4560,
                'description' => 'Stylizacja brwi i rzęs: laminacja, henna, przedłużanie rzęs.',
                'specialization' => 'Stylistka brwi i rzęs',
                'services' => [['Laminacja brwi', 120, 60], ['Henna brwi', 60, 30], ['Przedłużanie rzęs', 200, 120], ['Regulacja brwi', 40, 30]],
                'employees' => ['Weronika Maj', 'Dominika Lis'],
            ],
        ];
    }
}
