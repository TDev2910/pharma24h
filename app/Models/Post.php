<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Content\PostImage;

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
        'category_id',
    ];
    
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Content\Category::class);
    }

    //format date to d/m/Y
    protected $casts = [
        'is_published' => 'boolean',
        'created_at' => 'datetime:d/m/Y',
    ];

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }
}
