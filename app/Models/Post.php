<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Laravel\Scout\Searchable;
use App\Jobs\SendMail;
use Illuminate\Support\Facades\Bus;

class Post extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];
    protected $hidden = [];

    //show created_at


    protected $appends = [
        'images_paths',
        'likes_count',
        'loves_count',
        'celebrate_count',
        'total_reactions',
        "pending",
        "my_post",
        "votes",
    ]; //end of appends

    public function getImagesAttribute($value)
    {
        return json_decode($value);
    } //end of getImagesAttribute

    public function getImagesPathsAttribute()
    {
        if (!$this->images) return [];
        $images_paths = [];
        foreach ($this->images as $image) {
            array_push($images_paths, asset($image));
        }
        return $images_paths;
    } //end of retreving image directly

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d h:i:s A', strtotime($value));
    } //end of getCreatedAtAttribute

    public function getLikesCountAttribute()
    {
        return $this->likes->where('reaction', 1)->count();
    } //end of getReaction1CountAttribute

    public function getLovesCountAttribute()
    {
        return $this->likes->where('reaction', 2)->count();
    } //end of getReaction2CountAttribute

    public function getCelebrateCountAttribute()
    {
        return $this->likes->where('reaction', 3)->count();
    } //end of getReaction3CountAttribute

    public function getTotalReactionsAttribute()
    {
        return $this->likes->count();
    } //end of getTotalReactionsAttribute

    public function getPendingAttribute()
    {
        return $this->created_at > now();
    } //end of getPendingAttribute

    public function getMyPostAttribute()
    {
        return $this->user_id == auth('api')->id();
    } //end of getMyPostAttribute

    public function getVotesAttribute()
    {
        return $this->polls->sum('votes');
    } //end of getVotesAttribute

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

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    } //end of notification

    public function toSearchableArray()
    {
        return [
            "thread" => $this->thread,
        ];
    }
}
