<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'summary',
        'content',
        'is_published',
        'user_id',
    ];
    
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //format date to d/m/Y
    protected $casts = [
        'is_published' => 'boolean',
        'created_at' => 'datetime:d/m/Y',
    ];
}
