<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
        protected $table = 'properties';
          protected $fillable = [
        'type', 'title', 'description', 'status', 'location',
        'price', 'start_date', 'end_date', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
