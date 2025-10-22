<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_type',
        'rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Review thuộc về User
     */
    public function user() //tạo quan hệ với bảng users
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relationship: Review thuộc về Product (Medicine hoặc Goods)
     */
    public function product() //tạo quan hệ với bảng medicines và goods
    {
        return $this->morphTo();
    }

    /**
     * Scopes - Để query dễ dàng
     */
    public function scopeApproved($query) 
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForProduct($query, $productId, $productType)
    {
        return $query->where('product_id', $productId)
                    ->where('product_type', $productType);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Accessors
     */
    public function getIsOwnedByAttribute()
    {
        return $this->user_id === auth()->id();
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->diffForHumans(); // "2 ngày trước"
    }

    /**
     * Helper methods
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }
}
