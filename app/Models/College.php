<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $guarded = [];

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
}
