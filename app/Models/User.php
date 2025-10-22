<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'avatar',
        'phone',
        'address', 
        'province',
        'district',
        'ward',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get avatar URL attribute
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && file_exists(public_path('storage/avatars/' . $this->avatar))) {
            return asset('storage/avatars/' . $this->avatar);
        }
    
        return asset('images/default-avatar.svg');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews() //tạo quan hệ với bảng product_reviews
    {
        return $this->hasMany(ProductReview::class);
    }
}
