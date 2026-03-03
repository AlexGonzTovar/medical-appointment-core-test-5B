<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Doctor;

class DoctorTable extends DataTableComponent
{
    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return Doctor::query()
            ->with('user', 'speciality');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("User", "user.name")
                ->sortable(),
            Column::make("Especialidad", "speciality.name")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.doctors.actions',
                        ['doctor' => $row]);
                }),
        ];
    }
}
