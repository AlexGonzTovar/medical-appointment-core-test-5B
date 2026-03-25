<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Mail\AdminDailyReportMail;
use App\Mail\DoctorDailyReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDailyAppointmentsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:daily-report {--date= : The date to run the report for (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a daily report of today\'s appointments to the admin and to each doctor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('date')) {
            \Carbon\Carbon::setTestNow($this->option('date'));
        }

        $today = \Carbon\Carbon::today()->toDateString();

        // 1. Obtener citas de hoy ordenadas por hora
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.speciality'])
            ->where('date', $today)
            ->whereIn('status', ['Programado', 'Completado'])
            ->orderBy('start_time', 'asc')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments for today.');
            return;
        }

        // 2. Enviar Reporte Global al Administrador
        $adminEmail = env('ADMIN_EMAIL');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AdminDailyReportMail($appointments));
                $this->info("Global report sent to admin: {$adminEmail}");
            } catch (\Exception $e) {
                Log::error("Failed to send admin report: " . $e->getMessage());
                $this->error("Failed to send admin report.");
            }
        } else {
            $this->warn('ADMIN_EMAIL not defined in .env -> Skipping Admin Report');
        }

        // 3. Agrupar citas por Doctor y enviar reporte individual
        $appointmentsByDoctor = $appointments->groupBy('doctor_id');

        foreach ($appointmentsByDoctor as $doctorId => $doctorAppointments) {
            $doctor = $doctorAppointments->first()->doctor;
            $doctorEmail = $doctor->user->email ?? null;
            $doctorName = $doctor->user->name . ' ' . $doctor->user->last_name;

            if ($doctorEmail) {
                try {
                    Mail::to($doctorEmail)->send(new DoctorDailyReportMail($doctorAppointments, $doctorName));
                    $this->info("Report sent to doctor: {$doctorEmail}");
                } catch (\Exception $e) {
                    Log::error("Failed to send report to doctor {$doctorEmail}: " . $e->getMessage());
                }
            }
        }

        $this->info('Daily reports generated successfully.');
    }
}
