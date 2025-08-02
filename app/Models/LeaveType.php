<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'typical_annual_requests',
    ];


    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveSummaries(): HasMany
    {
        return $this->hasMany(LeaveSummary::class);
    }
}
