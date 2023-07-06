<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'image_path'
    ]; //end of appends

    public function getImagePathAttribute()
    {
        return asset($this->image);
    } //end of retreving image directly

}
