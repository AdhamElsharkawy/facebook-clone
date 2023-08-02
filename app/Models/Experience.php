<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Carbon\Carbon;

class Experience extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $appends = ['duration'];

    public function getDurationAttribute()
    {
        if ($this->is_current) {
            $startDate = Carbon::parse($this->start_date);
            return $startDate->diffForHumans();
        } else {
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);
            return $startDate->diff($endDate)->format('%y yrs, %m mos');
        }
        
    } //end of getDurationAttribute

    // public function getTypeAttribute($value)
    // {
    //     if ($value == 1) {
    //         return 'Full Time';
    //     } elseif ($value == 2) {
    //         return 'Part Time';
    //     } elseif ($value == 3) {
    //         return 'Internship';
    //     }
    // } //end of getTypeAttribute

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
        if ($this->is_current) {
            return "Present";
        } else {
            return date('Y-m-d', strtotime($value));
        }
    } //end of getEndDateAttribute

    public function company()
    {
        return $this->belongsTo(Company::class);
    } // end company

    public function user()
    {
        return $this->belongsTo(User::class);
    } // end user

    public function toSearchableArray()
    {
        return [
            "title" => $this->title,
            "description" => $this->description,
            "type" => $this->type,
        ];
    }
}
