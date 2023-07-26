<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Notification extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    } //end of user

    public function post()
    {
        return $this->belongsTo(Post::class);
    } //end of post
}
