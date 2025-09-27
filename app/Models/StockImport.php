<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_code',
        'supplier_id',
        'import_date',
        'status',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'note'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(StockImportItem::class);
    }

    public function payments()
    {
        return $this->hasMany(StockImportPayment::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
