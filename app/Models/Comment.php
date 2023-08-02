<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Comment extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $appends = [
        'images_paths',
        'likes_count',
        'loves_count',
        'celebrate_count',
        'total_reactions',
        'my_comment',
        'my_like',
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

    public function getMyCommentAttribute()
    {
        return $this->user_id == auth('api')->id();
    } //end of getMyCommentAttribute

    public function getMyLikeAttribute()
    {
        $like = $this->likes()->select('reaction','user_id','post_id')->where('user_id', auth('api')->id())->first();
        if (!$like) return false;
        return $like->reaction;
    } //end of getMyLikeAttribute

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
