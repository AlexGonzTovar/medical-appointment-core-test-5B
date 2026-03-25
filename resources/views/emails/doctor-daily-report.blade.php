<!DOCTYPE html>
<html>
<head>
    <title>Tus Citas de Hoy</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; color: #555; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Hola Dr(a). {{ $doctorName }},</h2>
    <p>Esta es tu agenda de citas programadas para el {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}:</p>

    @if($appointments->isEmpty())
        <p>No tienes citas programadas para el día de hoy. ¡Que tengas un excelente día!</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Estado de Cita</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                        <td>{{ $appointment->patient->user->name ?? 'N/A' }} {{ $appointment->patient->user->last_name ?? '' }}</td>
                        <td>{{ $appointment->status }}</td>
                        <td>{{ $appointment->reason ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin-top: 30px; font-size: 12px; color: #777;">Gracias por tu excelente labor en {{ config('app.name') }}.</p>
</body>
</html>
