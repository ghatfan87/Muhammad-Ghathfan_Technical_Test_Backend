<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;
    protected $fillable = [

        'lead_id',
        'approved_by',
        'rejected_by',
        'requested_by',
        'survey_status',
        'notes',
        'image',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Relasi ke tabel User (Operational yang melakukan approval).
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    public function requested_by()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
