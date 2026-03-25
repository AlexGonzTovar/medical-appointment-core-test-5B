<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;

class CreateAppointment extends Component
{
    public $date;
    public $speciality_id;
    public $time; // opcional por UI
    public $reason;

    public $availableDoctors = [];
    public $availablePatients = [];
    
    // Para el modal de confirmación
    public $selectedDoctorId = null;
    public $selectedTimeSlot = null;
    public $selectedPatientId = null;
    public $isConfirmModalOpen = false;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->availablePatients = Patient::with('user')->get();
    }

    public function searchAvailability()
    {
        $this->validate([
            'date' => 'required|date|after_or_equal:today',
            'speciality_id' => 'nullable|exists:specialities,id',
            'time' => 'nullable|date_format:H:i',
        ]);

        $dayOfWeek = Carbon::parse($this->date)->locale('es')->isoFormat('dddd');
        $dayOfWeek = ucfirst($dayOfWeek);

        // Map English to Spanish days
        $daysMap = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];
        $englishDay = Carbon::parse($this->date)->format('l');
        $searchDay = $daysMap[$englishDay] ?? $dayOfWeek;


        $query = Doctor::with(['user', 'speciality', 'schedules' => function ($q) use ($searchDay) {
            $q->where('day', $searchDay);
        }])->whereHas('schedules', function ($q) use ($searchDay) {
            $q->where('day', $searchDay);
        });

        if ($this->speciality_id) {
            $query->where('speciality_id', $this->speciality_id);
        }

        $doctors = $query->get();
        $this->availableDoctors = [];

        foreach ($doctors as $doctor) {
            $takenSlots = Appointment::where('doctor_id', $doctor->id)
                ->where('date', $this->date)
                ->whereIn('status', ['Programado', 'Completado'])
                ->get()
                ->map(fn($app) => $app->start_time . '-' . $app->end_time)
                ->toArray();

            $availableSlots = collect();
            
            foreach ($doctor->schedules as $schedule) {
                // Si el operario especificó una hora exacta a buscar y esta franja no es, la saltamos.
                if ($this->time && Carbon::parse($schedule->start_time)->format('H:i') !== $this->time) {
                    continue;
                }

                $slotString = $schedule->start_time . '-' . $schedule->end_time;
                if (!in_array($slotString, $takenSlots)) {
                    $availableSlots->push($schedule);
                }
            }

            if ($availableSlots->isNotEmpty()) {
                $doctorData = $doctor->toArray();
                $doctorData['user_name'] = $doctor->user->name . ' ' . $doctor->user->last_name;
                $doctorData['speciality_name'] = $doctor->speciality->name;
                
                // Conversión segura de \stdClass a array
                $slotsArray = [];
                foreach ($availableSlots as $slot) {
                    $slotsArray[] = [
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                    ];
                }
                $doctorData['available_slots'] = $slotsArray;
                
                $this->availableDoctors[] = $doctorData;
            }
        }
    }

    public function selectSlot($doctorId, $slotStartTime, $slotEndTime)
    {
        $this->selectedDoctorId = $doctorId;
        $this->selectedTimeSlot = [
            'start' => $slotStartTime,
            'end' => $slotEndTime,
        ];
        $this->selectedPatientId = null;
        $this->isConfirmModalOpen = true;
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->selectedDoctorId = null;
        $this->selectedTimeSlot = null;
        $this->selectedPatientId = null;
    }

    public function registerAppointment()
    {
        $this->validate([
            'selectedPatientId' => 'required|exists:patients,id',
            'reason' => 'required|string|max:1000',
        ], [
            'selectedPatientId.required' => 'Debes seleccionar un paciente para agendar la cita.',
            'reason.required' => 'Debes escribir el motivo de la cita.',
        ]);

        Appointment::create([
            'patient_id' => $this->selectedPatientId,
            'doctor_id' => $this->selectedDoctorId,
            'date' => $this->date,
            'start_time' => $this->selectedTimeSlot['start'],
            'end_time' => $this->selectedTimeSlot['end'],
            'status' => 'Programado',
            'reason' => $this->reason,
        ]);

        $this->closeConfirmModal();
        $this->reset('reason'); // clean it up afterwards
        
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita confirmada',
            'text' => 'La cita ha sido agendada exitosamente.'
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        $specialities = Speciality::all();
        return view('livewire.admin.appointments.create-appointment', compact('specialities'));
    }
}
