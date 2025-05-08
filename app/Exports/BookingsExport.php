<?php

namespace App\Exports;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Booking::with(['table', 'user']);

        // Apply the same filters as in the controller
        if ($this->request->has('search') && !empty($this->request->search)) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('table', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($this->request->has('status') && !empty($this->request->status)) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->has('date_from') && !empty($this->request->date_from)) {
            $dateFrom = Carbon::parse($this->request->date_from)->startOfDay();
            $query->where('start_time', '>=', $dateFrom);
        }

        if ($this->request->has('date_to') && !empty($this->request->date_to)) {
            $dateTo = Carbon::parse($this->request->date_to)->endOfDay();
            $query->where('start_time', '<=', $dateTo);
        }

        // Sort by start time by default
        $sortColumn = $this->request->sort ?? 'start_time';
        $sortDirection = $this->request->direction ?? 'desc';
        
        return $query->orderBy($sortColumn, $sortDirection)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Email',
            'Meja',
            'Kapasitas',
            'Mulai',
            'Selesai',
            'Durasi (jam)',
            'Status',
            'Dibuat Pada',
        ];
    }

    public function map($booking): array
    {
        $startTime = Carbon::parse($booking->start_time);
        $endTime = Carbon::parse($booking->end_time);
        $duration = $startTime->diffInHours($endTime);

        return [
            $booking->id,
            $booking->user->name,
            $booking->user->email,
            $booking->table->name,
            $booking->table->capacity . ' orang',
            $startTime->format('d/m/Y H:i'),
            $endTime->format('d/m/Y H:i'),
            $duration,
            ucfirst($booking->status),
            Carbon::parse($booking->created_at)->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headers)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0'],
                ],
            ],
        ];
    }
}