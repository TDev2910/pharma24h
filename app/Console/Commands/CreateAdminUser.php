<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email} {password} {--name=Admin Suckhoe24h}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo tài khoản admin cho hệ thống Suckhoe24h';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name');

        // Kiểm tra email đã tồn tại chưa
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Email {$email} đã tồn tại!");
            return 1;
        }

        try {
            // Tạo tài khoản admin
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'address' => '123 Đường ABC',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Dịch Vọng',
                'role' => 'admin'
            ]);

            $this->info("✅ Tạo tài khoản admin thành công!");
            $this->table(
                ['Thông tin', 'Giá trị'],
                [
                    ['Email', $email],
                    ['Password', $password],
                    ['Name', $name],
                    ['Role', 'admin']
                ]
            );

        } catch (\Exception $e) {
            $this->error("❌ Lỗi: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 