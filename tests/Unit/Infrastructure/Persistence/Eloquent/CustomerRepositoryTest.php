<?php

namespace Tests\Unit\Infrastructure\Persistence\Eloquent;

use Tests\TestCase;
use App\Models\User;
use App\Core\Customer\Domain\DTOs\CustomerData;
use App\Infrastructure\Persistence\Eloquent\CustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerRepositoryTest extends TestCase
{
    use RefreshDatabase; 

    private CustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CustomerRepository();
    }

    public function test_can_create_new_customer()
    {
        $dto = new CustomerData(
            name: 'Pikachu',
            email: 'pikachu@pokemon.com',
            phone: '0999999999',
            password: 'password123',
            address: 'Pallet Town'
        );

        $customer = $this->repository->create($dto);

        $this->assertInstanceOf(User::class, $customer);
        $this->assertEquals('Pikachu', $customer->name);
        $this->assertEquals('user', $customer->role);
        
        // Xác minh dữ liệu đã vào Database
        $this->assertDatabaseHas('users', [
            'email' => 'pikachu@pokemon.com',
            'phone' => '0999999999',
            'role' => 'user'
        ]);
    }

    public function test_can_count_active_customers()
    {
        //user active
        User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        
        //user không active
        User::factory()->create(['role' => 'user', 'email_verified_at' => null]);
        
        //admin 
        User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $activeCount = $this->repository->countActiveCustomers();

        $this->assertEquals(2, $activeCount);
    }

    public function test_can_update_customer()
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Old Name',
        ]);

        $dto = new CustomerData(
            name: 'New Name',
            email: $user->email, 
            phone: '0111222333',
        );

        $result = $this->repository->update($user, $dto);

        $this->assertTrue($result);
        $this->assertEquals('New Name', $user->fresh()->name);
        $this->assertEquals('0111222333', $user->fresh()->phone);
    }

    public function test_can_delete_customer()
    {
        $user = User::factory()->create(['role' => 'user']);

        $result = $this->repository->delete($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
