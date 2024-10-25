<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_name',
        'email',
        'phone_number',
        'survey_status',
        'sales_type',
        'notes',
        'assigned_to',
        'image'
    ];

     /**
     * Relasi ke model User (Salesperson)
     */
    public function salesperson()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($lead) {
            $assignedUser = User::find($lead->assigned_to);
            if ($assignedUser && $assignedUser->role !== 'salesperson') {
                $lead->sales_type = null;
            }
        });
    }
}
