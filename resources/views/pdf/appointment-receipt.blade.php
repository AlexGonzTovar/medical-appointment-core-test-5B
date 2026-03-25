<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Cita Médica</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 24px; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details th, .details td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        .details th { width: 30%; color: #555; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Comprobante de Cita Médica</p>
    </div>

    <table class="details">
        <tr>
            <th>ID Cita:</th>
            <td>#{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <th>Paciente:</th>
            <td>{{ $appointment->patient->user->name }} {{ $appointment->patient->user->last_name }}</td>
        </tr>
        <tr>
            <th>Doctor(a):</th>
            <td>{{ $appointment->doctor->user->name }} {{ $appointment->doctor->user->last_name }} ({{ $appointment->doctor->speciality->name ?? 'Especialista' }})</td>
        </tr>
        <tr>
            <th>Fecha Agendada:</th>
            <td>{{ \Carbon\Carbon::parse($appointment->date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</td>
        </tr>
        <tr>
            <th>Hora:</th>
            <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
        </tr>
        <tr>
            <th>Estado:</th>
            <td>{{ $appointment->status }}</td>
        </tr>
        <tr>
            <th>Motivo de la Cita:</th>
            <td>{{ $appointment->reason ?? 'No especificado' }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Este documento es un comprobante generado automáticamente.</p>
        <p>Gracias por confiar en nuestros servicios médicos.</p>
    </div>
</body>
</html>
