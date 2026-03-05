<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['user_id', 'subject', 'score', 'semester'];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
