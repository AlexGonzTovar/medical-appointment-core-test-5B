<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;

class AppointmentTable extends DataTableComponent
{
    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return Appointment::query()
            ->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
            Column::make("Paciente", "patient.user.name")
                ->searchable()
                ->sortable(),
            Column::make("Doctor", "doctor.user.name")
                ->searchable()
                ->sortable(),
            Column::make("Fecha", "date")
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d/m/Y'))
                ->sortable(),
            Column::make("Hora", "start_time")
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('H:i'))
                ->sortable(),
            Column::make("Hora fin", "end_time")
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('H:i'))
                ->sortable(),
            Column::make("Estado", "status")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    // Cargar vista de acciones para la tabla
                    return view('admin.appointments.actions', ['appointment' => $row]);
                }),
        ];
    }
}
