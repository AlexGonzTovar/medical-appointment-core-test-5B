<?php

namespace App\Livewire\Admin\Consultations;

use App\Models\Appointment;
use App\Models\Consultation;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ManageConsultation extends Component
{
    public Appointment $appointment;
    public ?Consultation $consultation = null;

    public $activeTab = 'consulta';

    // Consultation fields
    public $diagnosis = '';
    public $treatment = '';
    public $notes = '';

    // Prescriptions array
    public $prescriptionItems = [];

    // Modals
    public $showHistoryModal = false;
    public $showPreviousConsultationsModal = false;
    public $previousConsultations = [];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment->load(['patient.user', 'patient.bloodType']);
        
        // Load existing consultation if any
        $this->consultation = $appointment->consultation;
        
        if ($this->consultation) {
            $this->diagnosis = $this->consultation->diagnosis;
            $this->treatment = $this->consultation->treatment;
            $this->notes = $this->consultation->notes;
            
            // Load existing prescriptions
            $items = $this->consultation->prescriptionItems()->get();
            foreach ($items as $item) {
                $this->prescriptionItems[] = [
                    'medication' => $item->medication,
                    'dose' => $item->dose,
                    'frequency_duration' => $item->frequency_duration,
                ];
            }
        }
        
        // Add an empty prescription item by default if empty
        if (empty($this->prescriptionItems)) {
            $this->addMedication();
        }
    }

    public function addMedication()
    {
        $this->prescriptionItems[] = [
            'medication' => '',
            'dose' => '',
            'frequency_duration' => '',
        ];
    }

    public function removeMedication($index)
    {
        unset($this->prescriptionItems[$index]);
        $this->prescriptionItems = array_values($this->prescriptionItems); // Re-index array
    }

    public function saveConsultation()
    {
        $this->validate([
            'diagnosis' => 'required',
            'prescriptionItems.*.medication' => 'required_with:prescriptionItems.*.dose',
        ], [
            'diagnosis.required' => 'El diagnóstico es obligatorio.',
            'prescriptionItems.*.medication.required_with' => 'Debe escribir el nombre del medicamento.',
        ]);

        // Guardar o actualizar la consulta
        $this->consultation = Consultation::updateOrCreate(
            ['appointment_id' => $this->appointment->id],
            [
                'diagnosis' => $this->diagnosis,
                'treatment' => $this->treatment,
                'notes' => $this->notes,
            ]
        );

        // Limpiar recetas anteriores (sincronizar)
        $this->consultation->prescriptionItems()->delete();

        // Guardar las nuevas recetas
        foreach ($this->prescriptionItems as $item) {
            if (!empty(trim($item['medication']))) {
                $this->consultation->prescriptionItems()->create([
                    'medication' => $item['medication'],
                    'dose' => $item['dose'] ?? '',
                    'frequency_duration' => $item['frequency_duration'] ?? '',
                ]);
            }
        }

        // Según el usuario, la cita debe mantenerse como "Programado", no cambia de estado.

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Consulta Guardada',
            'text' => 'Los datos de la consulta y la receta se han guardado exitosamente.'
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function openPreviousConsultations()
    {
        $this->previousConsultations = Appointment::with(['doctor.user', 'consultation'])
            ->where('patient_id', $this->appointment->patient_id)
            ->where('id', '!=', $this->appointment->id) // Exclude current
            ->whereHas('consultation') // Only those that have a consultation saved
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $this->showPreviousConsultationsModal = true;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.consultations.manage-consultation');
    }
}

