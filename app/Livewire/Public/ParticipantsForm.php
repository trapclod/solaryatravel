<?php

namespace App\Livewire\Public;

use App\Models\Booking;
use Livewire\Component;

class ParticipantsForm extends Component
{
    public Booking $booking;

    /**
     * @var array<int, array{id:int, first_name:string, last_name:string, tax_code:string, dob:string|null, is_child:bool, label:string}>
     */
    public array $seats = [];

    public string $successMessage = '';

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['seatRecords.ageBracket']);

        foreach ($this->booking->seatRecords as $seat) {
            $bracket = $seat->ageBracket;
            $isChild = $bracket !== null;
            $this->seats[] = [
                'id' => $seat->id,
                'first_name' => $seat->guest_first_name ?? '',
                'last_name' => $seat->guest_last_name ?? '',
                'tax_code' => $seat->tax_code ?? '',
                'dob' => $seat->guest_date_of_birth?->format('Y-m-d'),
                'is_child' => $isChild,
                'is_primary' => (bool) $seat->is_primary,
                'label' => $isChild
                    ? ($bracket->label . ' · ' . ($seat->guest_date_of_birth?->format('d/m/Y') ?? ''))
                    : ($seat->is_primary ? 'Adulto (prenotante)' : 'Adulto'),
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'seats.*.first_name' => 'required|string|max:100',
            'seats.*.last_name' => 'required|string|max:100',
            'seats.*.tax_code' => 'required|string|max:32',
        ], [
            'seats.*.first_name.required' => 'Inserisci il nome per ogni partecipante.',
            'seats.*.last_name.required' => 'Inserisci il cognome per ogni partecipante.',
            'seats.*.tax_code.required' => 'Inserisci il codice fiscale per ogni partecipante.',
        ]);

        foreach ($this->seats as $row) {
            $seat = $this->booking->seatRecords()->find($row['id']);
            if (!$seat) {
                continue;
            }
            $seat->update([
                'guest_first_name' => trim($row['first_name']),
                'guest_last_name' => trim($row['last_name']),
                'tax_code' => strtoupper(trim($row['tax_code'])),
            ]);
        }

        $this->booking->forceFill(['participants_completed_at' => now()])->save();

        $this->successMessage = 'Dati salvati. Grazie!';
    }

    public function render()
    {
        return view('livewire.public.participants-form');
    }
}
