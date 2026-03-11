<?php

namespace Tests\Unit\Core\Doctor\Application\Services;

use Tests\TestCase;
use Mockery;
use App\Models\Doctor;
use App\Core\Doctor\Domain\DTOs\DoctorData;
use App\Core\Doctor\Application\Services\DoctorService;
use App\Core\Doctor\Ports\Outbound\DoctorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorServiceTest extends TestCase
{
    private $repositoryMock;
    private $doctorService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo Mock cho Repository
        $this->repositoryMock = Mockery::mock(DoctorRepositoryInterface::class);
        
        // Inject Mock Repository vào Service
        $this->doctorService = new DoctorService($this->repositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_dashboard_data_returns_correct_structure()
    {
        // 1. Chuẩn bị dữ liệu giả (Arrange)
        $doctor = new Doctor([
            'id' => 1,
            'name' => 'Test Doctor',
            'email' => 'doctor@example.com',
            'phone' => '0123456789',
        ]);

        $paginator = new LengthAwarePaginator([$doctor], 1, 15);

        // 2. Định nghĩa hành vi Mock (Expectations)
        $this->repositoryMock
            ->shouldReceive('getPaginatedDoctors')
            ->once()
            ->with('bác sĩ', 10)
            ->andReturn($paginator);

        // 3. Thực thi hàm cần test (Act)
        $result = $this->doctorService->getDashboardData('bác sĩ', 10);

        // 4. Kiểm tra kết quả (Assert)
        $this->assertArrayHasKey('doctors', $result);
        
        // Kiểm tra logic parse DTO/Array từ Entity trong hàm getDashboardData
        $firstDoctor = $result['doctors']->first();
        $this->assertEquals('Test Doctor', $firstDoctor['name']);
        $this->assertEquals('doctor@example.com', $firstDoctor['email']);
    }

    public function test_create_doctor_calls_repository()
    {
        $dto = new DoctorData(
            id: null,
            name: 'New Doctor',
            email: 'newdoctor@example.com',
            phone: '0987654321',
        );
        $createdDoctor = new Doctor(['id' => 2, 'name' => 'New Doctor']);

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($dto)
            ->andReturn($createdDoctor);

        $result = $this->doctorService->createDoctor($dto);

        $this->assertInstanceOf(Doctor::class, $result);
        $this->assertEquals('New Doctor', $result->name);
    }
}
