<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // protected $primaryKey = 'id'; // Ensure Laravel knows the primary key
    // public $incrementing = false; // Tell Laravel it's not auto-incrementing
    // protected $keyType = 'string'; // Specify that it's a string
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_name',
        'nickname',
        'user_email',
        'user_password',
        'user_type_id',
        'faculty_id',
        'gender',
        'user_photo_path',
        'delete_flag',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'user_password' => 'hashed',
        ];
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }
    
    public function userType()
    {
        return $this->belongsTo(userType::class, 'user_type_id', 'user_type_id');
    }
}
