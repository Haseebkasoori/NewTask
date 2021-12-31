<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKyc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'profile_image',
        'utility_bill_image',
        'cnic_image',
        'consignment',
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
