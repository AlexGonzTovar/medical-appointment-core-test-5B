<?php

namespace App\Observers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReceiptMail;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        \Illuminate\Support\Facades\Log::info('Appointment created event fired for ID: ' . $appointment->id);

        // Solo enviar en citas programadas
        if ($appointment->status === 'Programado') {
            $appointment->load(['patient.user', 'doctor.user']);

            $patientName = $appointment->patient->user->name;
            $patientPhone = $appointment->patient->user->phone;
            $doctorName = $appointment->doctor->user->name . ' ' . $appointment->doctor->user->last_name;

            \Illuminate\Support\Facades\Log::info("Patient phone: {$patientPhone}");

            // Format time and date
            $date = \Carbon\Carbon::parse($appointment->date)->format('d/m/Y');
            $time = \Carbon\Carbon::parse($appointment->start_time)->format('H:i');

            if ($patientPhone) {
                $message = "Hola {$patientName}, tu cita médica con el Dr(a). {$doctorName} para el {$date} a las {$time} horas ha sido confirmada con éxito. ¡Te esperamos!";

                $whatsappService = app(\App\Services\WhatsAppService::class);
                $whatsappService->sendMessage($patientPhone, $message);
            } else {
                \Illuminate\Support\Facades\Log::info('No phone number for patient.');
            }

            // --- Enviar Correo con PDF al Paciente y al Doctor ---
            try {
                $patientEmail = $appointment->patient->user->email;
                $doctorEmail = $appointment->doctor->user->email;

                if ($patientEmail) {
                    Mail::to($patientEmail)->send(new AppointmentReceiptMail($appointment));
                    \Illuminate\Support\Facades\Log::info("Email sent to Patient: {$patientEmail}");
                }

                if ($doctorEmail) {
                    Mail::to($doctorEmail)->send(new AppointmentReceiptMail($appointment));
                    \Illuminate\Support\Facades\Log::info("Email sent to Doctor: {$doctorEmail}");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error sending appointment receipt emails: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
