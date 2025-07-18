<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Department;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'images',
        'phone',
        'is_active',
        'last_login_at',
        'department_id',  
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean'
    ];


    // Add to the model class

    // Add accessor for easy image URL retrieval
    public function getImageUrlAttribute()
    {
        return $this->images 
            ? asset('storage/' . $this->images) 
            : asset('images/default-avatar.png'); 
    }

    protected static function booted()
    {
        static::deleted(function ($user) {
            if ($user->images) {
                Storage::disk('public')->delete($user->images);
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }



}
