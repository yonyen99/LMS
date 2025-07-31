<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'department_id',
        'report_date',
        'available_actual',
        'available_simulated',
        'entitled',
        'taken',
        'planned',
        'requested',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Unauthorized');
        }
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
