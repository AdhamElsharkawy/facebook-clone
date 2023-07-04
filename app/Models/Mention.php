<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
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

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    } //end of comment
}
