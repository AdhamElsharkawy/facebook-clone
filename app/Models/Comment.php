<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    } //end of post

    public function user()
    {
        return $this->belongsTo(User::class);
    } //end of user

    public function likes()
    {
        return $this->hasMany(Like::class);
    } //end of like

    public function mentions()
    {
        return $this->hasMany(Mention::class);
    } //end of mention
}
