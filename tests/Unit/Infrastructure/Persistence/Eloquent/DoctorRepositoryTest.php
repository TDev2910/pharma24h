<?php

namespace Tests\Unit\Infrastructure\Persistence\Eloquent;

use Tests\TestCase;
use App\Models\Doctor;
use App\Core\Doctor\Domain\DTOs\DoctorData;
use App\Infrastructure\Persistence\Eloquent\DoctorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DoctorRepositoryTest extends TestCase
{
    use RefreshDatabase; 

    private DoctorRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new DoctorRepository();
    }

    public function test_can_create_new_doctor()
    {
        $dto = new DoctorData(
            id: null,
            name: 'Pikachu Doctor',
            email: 'doctor@pokemon.com',
            phone: '0999999999',
            address: 'Pallet Town',
            specialization: 'Electric Healing',
            status: 'active'
        );

        $doctor = $this->repository->create($dto);

        $this->assertInstanceOf(Doctor::class, $doctor);
        $this->assertEquals('Pikachu Doctor', $doctor->name);
        
        // Xác minh dữ liệu đã vào Database
        $this->assertDatabaseHas('doctors', [
            'email' => 'doctor@pokemon.com',
            'phone' => '0999999999',
            'specialty' => 'Electric Healing',
            'status' => 'active'
        ]);
    }

    public function test_can_update_doctor()
    {
        // 1. Tạo sẵn trong DB (giả sử có factory)
        $doctor = Doctor::factory()->create([
            'name' => 'Old Name',
        ]);

        // 2. Chuẩn bị DTO mới
        $dto = new DoctorData(
            id: null,
            name: 'New Name',
            email: $doctor->email, 
            phone: '0111222333',
        );

        // 3. Thực thi
        $result = $this->repository->update($doctor, $dto);

        // 4. Kiểm tra 
        $this->assertTrue($result);
        $this->assertEquals('New Name', $doctor->fresh()->name);
        $this->assertEquals('0111222333', $doctor->fresh()->phone);
    }

    public function test_can_delete_doctor()
    {
        $doctor = Doctor::factory()->create();

        $result = $this->repository->delete($doctor);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }
}
