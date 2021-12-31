<?php

namespace App\Models;

use App\Http\Resources\PostResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
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
        'post_id',
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
    protected $table = 'comments';


    /**
     * Get the comments for the blog User.
     */
    public function Post()
    {
        return $this->hasOne(PostResource::class);
    }

}
