<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Company extends Model
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

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    } //end of experiences

    public function users()
    {
        return $this->belongsToMany(User::class, 'experiences')->withPivot('title', 'start_date', 'end_date', 'description', 'is_current', 'type');
    } //end of users

    public function toSearchableArray(){
        return [
            "name" => $this->name,
        ];
    }
}
