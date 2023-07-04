<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    } //end of experiences

    public function users()
    {
        return $this->belongsToMany(User::class, 'experiences')->withPivot('title', 'start_date', 'end_date', 'description', 'is_current', 'type');
    } //end of users
}
