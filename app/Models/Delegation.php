<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Delegation extends Model
{
   protected $fillable = [
    'delegator_id',
    'delegatee_id',
    'delegation_type', // Keep only one field for the type
    'start_date',
    'end_date'
];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    public function delegator()
    {
        return $this->belongsTo(User::class, 'delegator_id');
    }

    public function delegatee()
    {
        return $this->belongsTo(User::class, 'delegatee_id');
    }

    public function getStatusAttribute()
    {
        $today = Carbon::today();
        if (Carbon::parse($this->start_date)->gt($today)) {
            return 'upcoming';
        }
        if (Carbon::parse($this->end_date)->lt($today)) {
            return 'expired';
        }
        return 'active';
    }

    public function getStatusColorAttribute()
    {
        return [
            'active' => 'success',
            'upcoming' => 'info',
            'expired' => 'danger'
        ][$this->status] ?? 'secondary';
    }
}