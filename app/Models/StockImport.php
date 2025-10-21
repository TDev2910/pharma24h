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
        'total_discount',
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

    public function scopeFilterByDate($query,$from,$to)
    {
        if($from && $to)
        {
            return $query->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to);
            //Lọc tất cả đơn nhập hàng ví dụ 13/09 đến 15/09 (bao gồm cả ngày 13 và 15).
        }
        elseif($from)
        {
            return $query->whereDate('created_at', '>=', $from);
        }
        elseif($to)
        {
            return $query->whereDate('created_at', '<=', $to);
        }
    }
}
