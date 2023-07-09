<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Event extends Model
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

    public function toSearchableArray(){
        return [
            "name" => $this->name,
            "description" => $this->description,
            "location" => $this->location,
        ];
    }
}
