<!DOCTYPE html>
<html>
<head>
    <title>Comprobante de Cita - {{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hola,</h2>
    <p>Te adjuntamos el documento con el comprobante y los detalles de tu próxima cita.</p>
    
    <p><strong>Detalles rápidos:</strong></p>
    <ul>
        <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</li>
        <li><strong>Hora:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</li>
    </ul>

    <p>Por favor, revisa el archivo PDF adjunto a este correo para ver toda la información.</p>

    <p>Saludos cordiales,<br>
    <strong>El equipo de {{ config('app.name') }}</strong></p>
</body>
</html>
