<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['total_price', 'status'];

    public function user() {
        return $this->hasOne(User::class, 'user_id');
    }
}
