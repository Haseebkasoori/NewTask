<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'otp_mail',
        'otp_sms',
        'otp_authenticator',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];


    /**
     * The Table name.
     *
     */
    protected $table = 'two_factor_auth';


    /**
     * Get the comments for the blog User.
     */
    public function User()
    {
        return $this->belongsTo(User::class);
    }



}
