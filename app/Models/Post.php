<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    } //end of user

    public function polls()
    {
        return $this->hasMany(Poll::class);
    } //end of poll

    public function comments()
    {
        return $this->hasMany(Comment::class);
    } //end of comment

    public function likes()
    {
        return $this->hasMany(Like::class);
    } //end of like

    public function mentions()
    {
        return $this->hasMany(Mention::class);
    } //end of mention
}
