<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;
use App\Models\User;


class PatientTable extends DataTableComponent
{
    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return Patient::query()
            ->with('user');
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
            Column::make("Email", "user.email")
                ->sortable(),
            Column::make("ID Number", "user.id_number")
                ->sortable(),
            Column::make("Phone", "user.phone")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.patients.actions',
                        ['patient' => $row]);
                }),
        ];
    }
}
