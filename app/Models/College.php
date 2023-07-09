<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class College extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $appends = [
        'image_path'
    ]; //end of appends

    public function getImagePathAttribute()
    {
        return asset($this->image);
    } //end of retreving image directly

    public function users()
    {
        return $this->belongsToMany(User::class, 'educations')->withPivot('degree','major', 'start_date', 'end_date', 'description', 'is_current', 'location');
    } //end of users

    public function educations()
    {
        return $this->hasMany(Education::class);
    } //end of educations

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    } //end of certifications

    public function toSearchableArray(){
        return [
            "name" => $this->name,
        ];
    }
}
