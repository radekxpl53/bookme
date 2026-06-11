<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SystemFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_flow_works_correctly()
    {
        // 1. Rejestracja uzytkownika i utworzenie lokalu
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('biznes.lokale.store'), [
            'name' => 'Nowy Super Salon',
            'category' => 'Fryzjer',
            'address' => 'Warszawa 12',
            'description' => 'Test',
            'lat' => 52.22,
            'lon' => 21.01
        ]);
        
        $response->assertRedirect(route('biznes.lokale.index'));
        $this->assertDatabaseHas('businesses', ['name' => 'Nowy Super Salon', 'is_approved' => false]);
        $business = Business::where('name', 'Nowy Super Salon')->first();

        // 2. Dodawanie usługi
        $response = $this->post(route('biznes.lokale.uslugi.store', $business), [
            'name' => 'Strzyżenie Męskie',
            'price' => 50,
            'duration_minutes' => 30
        ]);
        
        $response->assertRedirect(route('biznes.lokale.uslugi.index', $business));
        $this->assertDatabaseHas('services', ['name' => 'Strzyżenie Męskie']);
        $service = Service::first();

        // 3. Dodawanie pracownika
        $response = $this->post(route('biznes.lokale.pracownicy.store', $business), [
            'name' => 'Jan Kowalski',
            'specialization' => 'Senior',
            'services' => [$service->id]
        ]);
        
        $response->assertRedirect(route('biznes.lokale.pracownicy.index', $business));
        $this->assertDatabaseHas('employees', ['name' => 'Jan Kowalski']);

        // 4. Edycja pracownika
        $employee = Employee::first();
        $response = $this->put(route('biznes.lokale.pracownicy.update', [$business, $employee]), [
            'name' => 'Janusz Kowalski',
            'specialization' => 'Ekspert',
            'services' => [$service->id]
        ]);
        
        $response->assertRedirect(route('biznes.lokale.pracownicy.index', $business));
        $this->assertDatabaseHas('employees', ['name' => 'Janusz Kowalski']);

        // 5. Admin zatwierdza salon
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        
        $response = $this->post(route('admin.businesses.approve', $business));
        $this->assertDatabaseHas('businesses', ['name' => 'Nowy Super Salon', 'is_approved' => true]);

        // 6. Usuwanie biznesu kaskadowo usuwa resztę (test)
        $this->actingAs($user); // wracamy do właściciela
        $response = $this->delete(route('biznes.lokale.destroy', $business));
        
        $response->assertRedirect(route('biznes.lokale.index'));
        $this->assertDatabaseMissing('businesses', ['name' => 'Nowy Super Salon']);
        $this->assertDatabaseMissing('services', ['name' => 'Strzyżenie Męskie']);
        $this->assertDatabaseMissing('employees', ['name' => 'Janusz Kowalski']);
    }
}
