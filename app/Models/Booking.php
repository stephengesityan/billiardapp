<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'user_id',
        'start_time',
        'end_time',
        'status',
        'payment_id',
        'payment_method',
        'total_amount',
        'payment_expired_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'payment_expired_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
