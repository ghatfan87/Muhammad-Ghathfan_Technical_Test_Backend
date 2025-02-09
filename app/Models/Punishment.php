<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punishment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',      
        'start_date',
        'end_date',
        'reason',
    ];

    public function salesperson()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
