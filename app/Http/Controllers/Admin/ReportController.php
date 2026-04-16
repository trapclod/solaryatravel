<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Catamaran;
use App\Models\Payment;
use App\Models\TimeSlot;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Revenue stats
        $revenue = Payment::where('status', PaymentStatus::SUCCEEDED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $previousRevenue = Payment::where('status', PaymentStatus::SUCCEEDED)
            ->whereBetween('created_at', [$startDate->copy()->subDays($startDate->diffInDays($endDate)), $startDate])
            ->sum('amount');

        // Bookings stats
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $confirmedBookings = Booking::where('status', BookingStatus::CONFIRMED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Passengers (using seats)
        $totalPassengers = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->sum('seats');

        // Average booking value
        $avgBookingValue = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->avg('total_amount') ?? 0;

        // Top catamarans
        $topCatamarans = Catamaran::withCount(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('booking_date', [$startDate, $endDate]);
        }])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        // Revenue by day chart data
        $revenueByDay = Payment::where('status', PaymentStatus::SUCCEEDED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Bookings by status
        $bookingsByStatus = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.reports.index', compact(
            'period',
            'startDate',
            'endDate',
            'revenue',
            'previousRevenue',
            'totalBookings',
            'confirmedBookings',
            'totalPassengers',
            'avgBookingValue',
            'topCatamarans',
            'revenueByDay',
            'bookingsByStatus'
        ));
    }

    /**
     * Revenue report.
     */
    public function revenue(Request $request): View
    {
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Daily revenue
        $dailyRevenue = Payment::where('status', PaymentStatus::SUCCEEDED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Revenue by catamaran
        $revenueByCatamaran = Booking::with('catamaran')
            ->whereHas('payments', function ($query) {
                $query->where('status', PaymentStatus::SUCCEEDED);
            })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->selectRaw('catamaran_id, SUM(total_amount) as total')
            ->groupBy('catamaran_id')
            ->orderByDesc('total')
            ->get();

        // Revenue by payment method
        $revenueByMethod = Payment::where('status', PaymentStatus::SUCCEEDED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        // Monthly comparison
        $monthlyRevenue = Payment::where('status', PaymentStatus::SUCCEEDED)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Total stats
        $stats = [
            'total' => Payment::where('status', PaymentStatus::SUCCEEDED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'transactions' => Payment::where('status', PaymentStatus::SUCCEEDED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'avg_transaction' => Payment::where('status', PaymentStatus::SUCCEEDED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount') ?? 0,
            'refunds' => Payment::where('status', PaymentStatus::REFUNDED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
        ];

        return view('admin.reports.revenue', compact(
            'period',
            'startDate',
            'endDate',
            'dailyRevenue',
            'revenueByCatamaran',
            'revenueByMethod',
            'monthlyRevenue',
            'stats'
        ));
    }

    /**
     * Bookings report.
     */
    public function bookings(Request $request): View
    {
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Daily bookings
        $dailyBookings = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(seats) as passengers')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Bookings by catamaran
        $bookingsByCatamaran = Booking::with('catamaran')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->selectRaw('catamaran_id, COUNT(*) as total, SUM(seats) as passengers')
            ->groupBy('catamaran_id')
            ->orderByDesc('total')
            ->get();

        // Bookings by status
        $bookingsByStatus = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            });

        // Bookings by time slot
        $bookingsByTimeSlot = Booking::with('timeSlot')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotNull('time_slot_id')
            ->selectRaw('time_slot_id, COUNT(*) as count')
            ->groupBy('time_slot_id')
            ->get()
            ->map(function ($item) {
                $item->time_slot = $item->timeSlot->name ?? 'N/A';
                return $item;
            });

        // Cancellation rate
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $cancelledBookings = Booking::where('status', BookingStatus::CANCELLED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 1) : 0;

        // Stats
        $stats = [
            'total' => $totalBookings,
            'confirmed' => Booking::where('status', BookingStatus::CONFIRMED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'completed' => Booking::where('status', BookingStatus::COMPLETED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'cancelled' => $cancelledBookings,
            'passengers' => Booking::whereBetween('created_at', [$startDate, $endDate])
                ->sum('seats'),
            'avg_passengers' => Booking::whereBetween('created_at', [$startDate, $endDate])
                ->avg('seats') ?? 0,
            'cancellation_rate' => $cancellationRate,
        ];

        return view('admin.reports.bookings', compact(
            'period',
            'startDate',
            'endDate',
            'dailyBookings',
            'bookingsByCatamaran',
            'bookingsByStatus',
            'bookingsByTimeSlot',
            'stats'
        ));
    }

    /**
     * Occupancy report.
     */
    public function occupancy(Request $request): View
    {
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Get catamarans with capacity
        $catamarans = Catamaran::where('is_active', true)->get();

        // Occupancy by catamaran
        $occupancyData = [];
        foreach ($catamarans as $catamaran) {
            $bookings = Booking::where('catamaran_id', $catamaran->id)
                ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->get();

            $totalPassengers = $bookings->sum('seats');
            $totalSlots = $bookings->count();
            $maxCapacity = $catamaran->max_capacity * $totalSlots;
            
            $occupancyData[] = [
                'catamaran' => $catamaran,
                'bookings' => $totalSlots,
                'passengers' => $totalPassengers,
                'max_capacity' => $maxCapacity,
                'occupancy_rate' => $maxCapacity > 0 ? round(($totalPassengers / $maxCapacity) * 100, 1) : 0,
                'avg_passengers' => $totalSlots > 0 ? round($totalPassengers / $totalSlots, 1) : 0,
            ];
        }

        // Sort by occupancy rate
        usort($occupancyData, fn($a, $b) => $b['occupancy_rate'] <=> $a['occupancy_rate']);

        // Daily occupancy trend
        $dailyOccupancy = Booking::whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->selectRaw('DATE(booking_date) as date, SUM(seats) as passengers, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Time slot popularity
        $timeSlotPopularity = Booking::with('timeSlot')
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotNull('time_slot_id')
            ->selectRaw('time_slot_id, COUNT(*) as count, SUM(seats) as passengers')
            ->groupBy('time_slot_id')
            ->get()
            ->map(function ($item) {
                $item->time_slot = $item->timeSlot->name ?? 'N/A';
                return $item;
            });

        // Day of week analysis
        $dayOfWeekStats = Booking::whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->selectRaw('DAYOFWEEK(booking_date) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Overall stats
        $stats = [
            'total_capacity_used' => array_sum(array_column($occupancyData, 'passengers')),
            'total_max_capacity' => array_sum(array_column($occupancyData, 'max_capacity')),
            'avg_occupancy' => count($occupancyData) > 0 
                ? round(array_sum(array_column($occupancyData, 'occupancy_rate')) / count($occupancyData), 1) 
                : 0,
            'busiest_day' => !empty($dayOfWeekStats) ? $this->getDayName(array_search(max($dayOfWeekStats), $dayOfWeekStats)) : '-',
        ];

        return view('admin.reports.occupancy', compact(
            'period',
            'startDate',
            'endDate',
            'occupancyData',
            'dailyOccupancy',
            'timeSlotPopularity',
            'dayOfWeekStats',
            'stats'
        ));
    }

    /**
     * Export data.
     */
    public function export(Request $request): Response
    {
        $type = $request->input('type', 'bookings');
        $format = $request->input('format', 'csv');
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $data = match($type) {
            'bookings' => $this->getBookingsExportData($startDate, $endDate),
            'revenue' => $this->getRevenueExportData($startDate, $endDate),
            'passengers' => $this->getPassengersExportData($startDate, $endDate),
            default => $this->getBookingsExportData($startDate, $endDate),
        };

        if ($format === 'csv') {
            return $this->exportToCsv($data, $type);
        }

        return $this->exportToCsv($data, $type);
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            'all' => Carbon::createFromDate(2020, 1, 1),
            default => now()->startOfMonth(),
        };
    }

    /**
     * Get day name from number.
     */
    private function getDayName(int $day): string
    {
        $days = [
            1 => 'Domenica',
            2 => 'Lunedì',
            3 => 'Martedì',
            4 => 'Mercoledì',
            5 => 'Giovedì',
            6 => 'Venerdì',
            7 => 'Sabato',
        ];
        return $days[$day] ?? '-';
    }

    /**
     * Get bookings export data.
     */
    private function getBookingsExportData(Carbon $startDate, Carbon $endDate): array
    {
        $bookings = Booking::with(['catamaran', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [['Numero', 'Data Prenotazione', 'Data Escursione', 'Catamarano', 'Cliente', 'Email', 'Posti', 'Totale', 'Stato']];

        foreach ($bookings as $booking) {
            $data[] = [
                $booking->booking_number,
                $booking->created_at->format('d/m/Y H:i'),
                $booking->booking_date->format('d/m/Y'),
                $booking->catamaran->name ?? '-',
                $booking->user->name ?? ($booking->customer_first_name . ' ' . $booking->customer_last_name),
                $booking->user->email ?? $booking->customer_email,
                $booking->seats,
                number_format($booking->total_amount, 2, ',', '.'),
                $booking->status->value,
            ];
        }

        return $data;
    }

    /**
     * Get revenue export data.
     */
    private function getRevenueExportData(Carbon $startDate, Carbon $endDate): array
    {
        $payments = Payment::with('booking')
            ->where('status', PaymentStatus::SUCCEEDED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [['Data', 'Prenotazione', 'Metodo', 'Importo', 'ID Transazione']];

        foreach ($payments as $payment) {
            $data[] = [
                $payment->created_at->format('d/m/Y H:i'),
                $payment->booking->booking_number ?? '-',
                $payment->payment_method,
                number_format($payment->amount, 2, ',', '.'),
                $payment->stripe_payment_intent_id ?? '-',
            ];
        }

        return $data;
    }

    /**
     * Get passengers export data.
     */
    private function getPassengersExportData(Carbon $startDate, Carbon $endDate): array
    {
        $bookings = Booking::with(['catamaran', 'user', 'timeSlot'])
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date', 'desc')
            ->get();

        $data = [['Data', 'Fascia Oraria', 'Catamarano', 'Prenotazione', 'Cliente', 'Posti Prenotati']];

        foreach ($bookings as $booking) {
            $data[] = [
                $booking->booking_date->format('d/m/Y'),
                $booking->timeSlot->name ?? '-',
                $booking->catamaran->name ?? '-',
                $booking->booking_number,
                $booking->user->name ?? ($booking->customer_first_name . ' ' . $booking->customer_last_name),
                $booking->seats,
            ];
        }

        return $data;
    }

    /**
     * Export data to CSV.
     */
    private function exportToCsv(array $data, string $type): Response
    {
        $filename = "{$type}_export_" . now()->format('Y-m-d_His') . ".csv";
        
        $content = '';
        foreach ($data as $row) {
            $content .= implode(';', array_map(fn($cell) => '"' . str_replace('"', '""', $cell) . '"', $row)) . "\n";
        }

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Content-Length', strlen($content));
    }
}
