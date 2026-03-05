<?php

namespace Tests\Unit\Core\Customer\Application\Services;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Core\Customer\Domain\DTOs\CustomerData;
use App\Core\Customer\Application\Services\CustomerService;
use App\Core\Customer\Ports\Outbound\CustomerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerServiceTest extends TestCase
{
    private $repositoryMock;
    private $customerService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo Mock cho Repository
        $this->repositoryMock = Mockery::mock(CustomerRepositoryInterface::class);
        
        // Inject Mock Repository vào Service
        $this->customerService = new CustomerService($this->repositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_dashboard_data_returns_correct_structure()
    {
        // 1. Chuẩn bị dữ liệu giả (Arrange)
        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0123456789',
        ]);
        // Giả lập accessor/mutator có sẵn nếu cần
        $user->setAttribute('orders_count', 5);
        $user->setAttribute('orders_sum_total_amount', 1000000);

        $paginator = new LengthAwarePaginator([$user], 1, 15);

        // 2. Định nghĩa hành vi Mock (Expectations)
        $this->repositoryMock
            ->shouldReceive('getPaginatedCustomers')
            ->once()
            ->with('khách hàng', 10)
            ->andReturn($paginator);

        $this->repositoryMock
            ->shouldReceive('countTotalCustomers')
            ->once()
            ->andReturn(100);

        $this->repositoryMock
            ->shouldReceive('countActiveCustomers')
            ->once()
            ->andReturn(80);

        // 3. Thực thi hàm cần test (Act)
        $result = $this->customerService->getDashboardData('khách hàng', 10);

        // 4. Kiểm tra kết quả (Assert)
        $this->assertArrayHasKey('customers', $result);
        $this->assertArrayHasKey('stats', $result);
        
        $this->assertEquals(100, $result['stats']['totalCustomers']);
        $this->assertEquals(80, $result['stats']['activeCustomers']);

        // Kiểm tra logic parse DTO/Array từ Entity trong hàm getDashboardData
        $firstCustomer = $result['customers']->first();
        $this->assertEquals('Test User', $firstCustomer['name']);
        $this->assertEquals('test@example.com', $firstCustomer['email']);
        $this->assertEquals(5, $firstCustomer['orders_count']);
        $this->assertEquals(1000000, $firstCustomer['total_amount']);
    }

    public function test_create_customer_calls_repository()
    {
        $dto = new CustomerData(
            name: 'New User',
            email: 'new@example.com',
            phone: '0987654321',
            password: 'password123'
        );

        $createdUser = new User(['id' => 2, 'name' => 'New User']);

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($dto)
            ->andReturn($createdUser);

        $result = $this->customerService->createCustomer($dto);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('New User', $result->name);
    }
}
