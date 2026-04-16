<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Carbon\Carbon;

class BookingCalendar extends Component
{
    public int $catamaranId;
    public array $availableDates = [];
    public ?string $selectedDate = null;
    public string $currentMonth;
    public int $currentYear;

    protected $listeners = ['refreshCalendar' => '$refresh'];

    public function mount(int $catamaranId, array $availableDates = [], ?string $selectedDate = null): void
    {
        $this->catamaranId = $catamaranId;
        $this->availableDates = $availableDates;
        $this->selectedDate = $selectedDate;
        $this->currentMonth = now()->format('m');
        $this->currentYear = now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        
        // Don't go before current month
        if ($date->lt(now()->startOfMonth())) {
            return;
        }

        $this->currentMonth = $date->format('m');
        $this->currentYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        
        // Don't go more than 6 months ahead
        if ($date->gt(now()->addMonths(6))) {
            return;
        }

        $this->currentMonth = $date->format('m');
        $this->currentYear = $date->year;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->dispatch('dateSelected', date: $date);
    }

    public function isDateAvailable(string $date): bool
    {
        return in_array($date, $this->availableDates);
    }

    public function isDatePast(string $date): bool
    {
        return Carbon::parse($date)->lt(now()->startOfDay());
    }

    public function getCalendarDays(): array
    {
        $startOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Start from Sunday (or Monday based on locale)
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);
        
        $days = [];
        $current = $startOfCalendar->copy();
        
        while ($current->lte($endOfCalendar)) {
            $dateString = $current->format('Y-m-d');
            $days[] = [
                'date' => $dateString,
                'day' => $current->day,
                'isCurrentMonth' => $current->month == $this->currentMonth,
                'isToday' => $current->isToday(),
                'isSelected' => $this->selectedDate === $dateString,
                'isAvailable' => $this->isDateAvailable($dateString),
                'isPast' => $this->isDatePast($dateString),
            ];
            $current->addDay();
        }
        
        return $days;
    }

    public function getMonthName(): string
    {
        return Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)
            ->locale('it')
            ->isoFormat('MMMM YYYY');
    }

    public function render()
    {
        return view('livewire.public.booking-calendar', [
            'days' => $this->getCalendarDays(),
            'monthName' => $this->getMonthName(),
            'weekDays' => ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'],
        ]);
    }
}
