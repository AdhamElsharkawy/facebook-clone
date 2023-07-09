<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Like extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    } //end of post

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    } //end of comment

    public function user()
    {
        return $this->belongsTo(User::class);
    } //end of user
}
