<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Department extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    } //end of users

    public function toSearchableArray(){
        return [
            "name" => $this->name,
        ];
    } //end of toSearchableArray
}
