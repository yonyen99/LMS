<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'duration',
        'reason',
        'status',
        'requested_at',
        'last_changed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'requested_at' => 'datetime',
        'last_changed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
    

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusChanges()
    {
        return $this->hasMany(LeaveStatusChange::class);
    }
}
