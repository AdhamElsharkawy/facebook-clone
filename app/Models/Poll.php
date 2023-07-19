<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Poll extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    } //end of post

    public function users()
    {
        return $this->belongsToMany(User::class, 'votes');
    } //end of user

    public function toSearchableArray(){
        return [
            "poll" => $this->poll,
        ];
    }
}
