<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'assigned_to',
        'client_name',
        'client_contact'
    ];
    public function lead()
    {
        return $this->hasOne(Lead::class);
    }
}
