<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getIsCurrentAttribute($value)
    {
        if ($value == 0) {
            return false;
        } elseif ($value == 1) {
            return true;
        }
    } //end of getIsCurrentAttribute

    public function getStartDateAttribute($value)
    {
        return date('Y-m-d', strtotime($value));
    } //end of getStartDateAttribute

    public function getEndDateAttribute($value)
    {
        return date('Y-m-d', strtotime($value));
    } //end of getEndDateAttribute

    public function user()
    {
        return $this->belongsTo(User::class);
    } //end of user

    public function college()
    {
        return $this->belongsTo(College::class);
    } //end of college
}
