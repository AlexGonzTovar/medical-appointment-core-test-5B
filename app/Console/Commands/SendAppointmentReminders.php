<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders to patients for their appointments scheduled for tomorrow.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        
        $appointments = \App\Models\Appointment::with(['patient.user', 'doctor.user'])
            ->where('date', $tomorrow)
            ->where('status', 'Programado')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("No appointments found for {$tomorrow}.");
            return;
        }

        $whatsappService = app(\App\Services\WhatsAppService::class);
        $count = 0;

        foreach ($appointments as $appointment) {
            $patientPhone = $appointment->patient->user->phone ?? null;
            
            if ($patientPhone) {
                $patientName = $appointment->patient->user->name;
                $doctorName = $appointment->doctor->user->name . ' ' . $appointment->doctor->user->last_name;
                $time = \Carbon\Carbon::parse($appointment->start_time)->format('H:i');

                $message = "Hola {$patientName}, este es un recordatorio de que mañana tienes una cita médica con el Dr(a). {$doctorName} a las {$time} horas. ¡Te esperamos!";
                
                $whatsappService->sendMessage($patientPhone, $message);
                $this->info("Reminder sent to {$patientName} ({$patientPhone}).");
                $count++;
            }
        }

        $this->info("Finished sending {$count} reminders.");
    }
}
