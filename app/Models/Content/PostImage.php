<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class PostImage extends Model
{
    use HasFactory;

    protected $fillable = ['post_id','path'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
