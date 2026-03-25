<!DOCTYPE html>
<html>
<head>
    <title>Reporte Diario de Citas</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; color: #555; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Reporte de Citas: {{ ucfirst(\Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</h2>
    <p>Hola Administrador, a continuación se muestra la lista de todas las citas programas para el día seleccionado en {{ config('app.name') }}:</p>

    @if($appointments->isEmpty())
        <p>No hay citas programadas para el día de hoy.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Doctor(a)</th>
                    <th>Especialidad</th>
                    <th>Estado</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                        <td>{{ $appointment->patient->user->name ?? 'N/A' }} {{ $appointment->patient->user->last_name ?? '' }}</td>
                        <td>{{ $appointment->doctor->user->name ?? 'N/A' }} {{ $appointment->doctor->user->last_name ?? '' }}</td>
                        <td>{{ $appointment->doctor->speciality->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->status }}</td>
                        <td>{{ $appointment->reason ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin-top: 30px; font-size: 12px; color: #777;">Este es un mensaje generado automáticamente.</p>
</body>
</html>
