<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ChatSession extends Model
{
    use HasUuids;
    protected $fillable = ['id', 'user_id', 'customer_name', 'customer_email', 'type', 'session_token'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
