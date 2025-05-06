<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'address', 'image'];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
