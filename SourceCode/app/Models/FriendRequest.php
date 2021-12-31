<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sender_id',
        'reciever_id',
        'status',
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
    protected $table = 'friend_request';


    /**
     * Get the Sender for the blog User.
     */
    public function Sender()
    {
        return $this->belongsTo(User::class,'sender_id','id');
    }

    /**
     * Get the comments for the blog User.
     */
    public function Reciever()
    {
        return $this->belongsTo(User::class,'reciever_id','id');
    }
}
