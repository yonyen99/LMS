<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'overtime_date',
        'time_period',
        'start_time',
        'end_time',
        'duration',
        'reason',
        'status',
        'requested_at',
        'last_changed_at',
        'action_by',
    ];

    protected $casts = [
        'overtime_date' => 'date:d/m/Y',
        'start_time' => 'string',
        'end_time' => 'string',
        'requested_at' => 'date:d/m/Y',
        'last_changed_at' => 'date:d/m/Y',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

      public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
