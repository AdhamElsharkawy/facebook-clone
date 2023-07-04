<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function college()
    {
        return $this->belongsTo(College::class);
    }//end of college

    public function user()
    {
        return $this->belongsTo(User::class);
    } //end of user
}
