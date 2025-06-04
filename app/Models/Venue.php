<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'image',
        'phone',          // Pastikan field ini ada
        'description',    // Pastikan field ini ada
        'open_time',      // Pastikan field ini ada
        'close_time',     // Pastikan field ini ada
    ];

    protected $casts = [
        'open_time' => 'datetime',
        'close_time' => 'datetime',
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
