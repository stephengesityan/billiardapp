<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Venue extends Model
{
    use HasFactory;

    public function getIsOvernightAttribute()
    {
        // Jika jam tutup lebih kecil dari jam buka, berarti melewati tengah malam
        return $this->close_time && $this->open_time && $this->close_time < $this->open_time;
    }

    protected $fillable = [
        'name',
        'address',
        'image',
        'phone',
        'description',
        'open_time',
        'close_time',
        'status',
        'close_reason',
        'reopen_date',
        'original_open_time',
        'original_close_time',
    ];

    protected $dates = [
        'reopen_date',
    ];

    // --- TAMBAHKAN PROPERTI INI ---
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_overnight'];
    // --- AKHIR DARI PENAMBAHAN ---

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    /**
     * Check if venue should automatically reopen
     */
    public function checkAutoReopen()
    {
        if ($this->status === 'close' && $this->reopen_date && Carbon::today()->gte($this->reopen_date)) {
            $this->update([
                'status' => 'open',
                'open_time' => $this->original_open_time,
                'close_time' => $this->original_close_time,
                'close_reason' => null,
                'reopen_date' => null,
                'original_open_time' => null,
                'original_close_time' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Close venue with reason and reopen date
     */
    public function closeVenue($reason, $reopenDate)
    {
        // Simpan jam operasional saat ini sebelum mengubahnya
        $currentOpenTime = $this->open_time;
        $currentCloseTime = $this->close_time;
        
        $this->update([
            'status' => 'close',
            'close_reason' => $reason,
            'reopen_date' => $reopenDate,
            'original_open_time' => $currentOpenTime,  // Simpan jam asli
            'original_close_time' => $currentCloseTime, // Simpan jam asli
            'open_time' => '00:00',  // Set ke 00:00 setelah menyimpan original
            'close_time' => '00:00', // Set ke 00:00 setelah menyimpan original
        ]);
    }

    /**
     * Open venue manually
     */
    public function openVenue()
    {
        $this->update([
            'status' => 'open',
            'open_time' => $this->original_open_time ?: $this->open_time,
            'close_time' => $this->original_close_time ?: $this->close_time,
            'close_reason' => null,
            'reopen_date' => null,
            'original_open_time' => null,
            'original_close_time' => null,
        ]);
    }
}