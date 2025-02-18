<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Post extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'title',
        'photo_id',
        'body',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
