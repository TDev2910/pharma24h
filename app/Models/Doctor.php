<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    protected $fillable = [
        'doctor_code',
        'name',
        'gender',
        'phone',
        'email',
        'avatar',
        'address',
        'province_district',
        'ward_commune',
        'specialty',
        'qualification',
        'note',
        'status',
    ];

    public static function generateDoctorCode() //tao mã bác sĩ random 6 chữ số
    {
        do
        {
            $code = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } 
        while (Doctor::where('doctor_code', $code)->exists());
        return $code;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
     // Accessor to display full address
     public function getFullAddressAttribute()
     {
         $address = $this->address;
         if ($this->ward_commune) $address .= ', ' . $this->ward_commune;
         if ($this->province_district) $address .= ', ' . $this->province_district;
         return $address;
     }
}
