<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getTypeAttribute($value)
    {
        if ($value == 1) {
            return 'Full Time';
        } elseif ($value == 2) {
            return 'Part Time';
        } elseif ($value == 3) {
            return 'Internship';
        }
    } //end of getTypeAttribute

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

    public function company()
    {
        return $this->belongsTo(Company::class);
    } // end company

    public function user()
    {
        return $this->belongsTo(User::class);
    } // end user
}
