<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Catamaran;
use App\Models\Payment;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Today's stats
        $todayStats = [
            'bookings' => Booking::whereDate('booking_date', today())->count(),
            'guests' => Booking::whereDate('booking_date', today())
                ->where('status', '!=', BookingStatus::CANCELLED)
                ->sum('seats'),
            'revenue' => Payment::whereDate('paid_at', today())
                ->where('status', PaymentStatus::COMPLETED)
                ->sum('amount'),
        ];

        // Monthly stats
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthlyStats = [
            'bookings' => Booking::whereBetween('booking_date', [$monthStart, $monthEnd])->count(),
            'revenue' => Payment::whereBetween('paid_at', [$monthStart, $monthEnd])
                ->where('status', PaymentStatus::COMPLETED)
                ->sum('amount'),
            'avg_booking_value' => Payment::whereBetween('paid_at', [$monthStart, $monthEnd])
                ->where('status', PaymentStatus::COMPLETED)
                ->avg('amount') ?? 0,
        ];

        // Pending bookings
        $pendingBookings = Booking::where('status', BookingStatus::PENDING)
            ->with(['catamaran', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Today's bookings
        $todayBookings = Booking::whereDate('booking_date', today())
            ->with(['catamaran', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Weekly revenue chart data
        $weeklyRevenue = Payment::where('status', PaymentStatus::COMPLETED)
            ->whereBetween('paid_at', [now()->subDays(7), now()])
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->locale('it')->isoFormat('ddd');
            $chartData[] = $weeklyRevenue[$date] ?? 0;
        }

        // Bookings by status
        $bookingsByStatus = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Popular catamarans
        $popularCatamarans = Catamaran::withCount(['bookings' => function ($query) use ($monthStart, $monthEnd) {
            $query->whereBetween('booking_date', [$monthStart, $monthEnd]);
        }])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        // Recent activity
        $recentActivity = collect();

        // Add recent bookings
        $recentBookings = Booking::with('catamaran')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'type' => 'booking',
                    'message' => "Nuova prenotazione #{$booking->booking_number}",
                    'details' => "{$booking->customer_first_name} {$booking->customer_last_name} - {$booking->catamaran->name}",
                    'time' => $booking->created_at,
                    'icon' => 'calendar',
                    'color' => 'blue',
                ];
            });

        // Add recent payments
        $recentPayments = Payment::with('booking')
            ->where('status', PaymentStatus::COMPLETED)
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment',
                    'message' => "Pagamento ricevuto €" . number_format($payment->amount, 2),
                    'details' => "Prenotazione #{$payment->booking->booking_number}",
                    'time' => $payment->paid_at,
                    'icon' => 'credit-card',
                    'color' => 'green',
                ];
            });

        $recentActivity = $recentBookings->merge($recentPayments)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return view('admin.dashboard', compact(
            'todayStats',
            'monthlyStats',
            'pendingBookings',
            'todayBookings',
            'chartLabels',
            'chartData',
            'bookingsByStatus',
            'popularCatamarans',
            'recentActivity'
        ));
    }
}
