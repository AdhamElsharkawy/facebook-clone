<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable, Searchable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ]; //end of hidden

    protected $appends = [
        'image_path'
    ]; //end of appends

    protected $casts = [
        'email_verified_at' => 'datetime',
    ]; //end of casts


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */


    public function getJWTIdentifier()
    {
        return $this->getKey();
    } //end of getJWTIdentifier

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    } //end of getJWTCustomClaims

    public function getImagePathAttribute()
    {
        return asset($this->image);
    } //end of retreving image directly

    public function getBirthDateAttribute($value)
    {
        return date('Y-m-d', strtotime($value));
    } //end of getBirthDateAttribute

    public function department()
    {
        return $this->belongsTo(Department::class);
    } //end of department

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    } //end of experience

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'experiences')->withPivot('title', 'start_date', 'end_date', 'description', 'is_current', 'type');
    } //end of companies

    public function educations()
    {
        return $this->hasMany(Education::class);
    } //end of educations

    public function colleges()
    {
        return $this->belongsToMany(College::class, 'educations')->withPivot('degree', 'major', 'start_date', 'end_date', 'description', 'is_current', 'location');
    } //end of colleges

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    } //end of certifications

    public function posts()
    {
        return $this->hasMany(Post::class);
    } //end of posts

    public function polls()
    {
        return $this->belongsToMany(Poll::class, 'votes');
    } //end of polls

    public function comments()
    {
        return $this->hasMany(Comment::class);
    } //end of comments

    public function likes()
    {
        return $this->hasMany(Like::class);
    } //end of likes

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    } //end of notifications

    public function mentions()
    {
        return $this->hasMany(Mention::class);
    } //end of mentions

    public function toSearchableArray()
    {
        return [
            "title" => $this->title,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
        ];
    }
}
