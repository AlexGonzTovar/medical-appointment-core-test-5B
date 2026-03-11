<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function edit(Doctor $doctor)
    {
        // Obtener los horarios existentes del doctor
        $schedules = $doctor->schedules()
            ->orderBy('start_time')
            ->get()
            ->groupBy('day')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return $item->start_time . '-' . $item->end_time;
                })->map(fn() => true);
            })->toArray();

        return view('admin.doctors.schedule', compact('doctor', 'schedules'));
    }

    public function store(Request $request, Doctor $doctor)
    {
        // Validar el request
        $request->validate([
            'schedules' => 'nullable|array',
            'schedules.*' => 'array',
        ]);

        // Primero borramos todos los horarios actuales
        $doctor->schedules()->delete();

        if ($request->has('schedules')) {
            $schedulesToInsert = [];
            
            // Recorrer los schedules seleccionados: ['Lunes' => ['08:00:00-08:30:00', ...], 'Martes' => [...]]
            foreach ($request->schedules as $day => $times) {
                foreach ($times as $timeRange) {
                    [$start_time, $end_time] = explode('-', $timeRange);
                    
                    $schedulesToInsert[] = [
                        'doctor_id' => $doctor->id,
                        'day' => $day,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Insertar de forma masiva
            if (count($schedulesToInsert) > 0) {
                DoctorSchedule::insert($schedulesToInsert);
            }
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Horario actualizado',
            'text' => 'El horario ha sido guardado correctamente.'
        ]);

        return redirect()->route('admin.doctors.schedule', $doctor);
    }
}
