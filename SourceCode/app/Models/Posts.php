<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'text',
        'attachment',
        'user_id',
        'visibility',
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
    protected $table = 'posts';


    /**
     * Get the comments for the blog User.
     */
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the blog User.
     */
    public function Comments()
    {
        return $this->hasMany(Comments::class,'post_id','id');
    }

}
