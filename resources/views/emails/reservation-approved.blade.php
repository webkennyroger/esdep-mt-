<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Aprovada</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #16a34a; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; color: #333; }
        .info-box { background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info-box p { margin: 5px 0; }
        .label { font-weight: bold; color: #1e40af; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reserva Aprovada</h1>
        </div>
        <div class="content">
            <p>Prezado(a) <strong>{{ $reservation->requester_name }}</strong>,</p>
            <p>Sua solicitação de reserva foi <strong style="color: #16a34a;">APROVADA</strong> com sucesso!</p>
            
            <div class="info-box">
                <p><span class="label">Evento:</span> {{ $reservation->event_name }}</p>
                <p><span class="label">Sala:</span> {{ $reservation->room->name }}</p>
                <p><span class="label">Data:</span> {{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y') }}</p>
                <p><span class="label">Horário:</span> {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</p>
                <p><span class="label">Participantes:</span> {{ $reservation->attendees_count }}</p>
            </div>
            
            <p>O evento já está visível no calendário público da escola.</p>
            <p>Atenciosamente,<br><strong>Equipe ESDEP-MT</strong></p>
        </div>
        <div class="footer">
            <p>Escola Superior da Defensoria Pública do Estado de Mato Grosso</p>
        </div>
    </div>
</body>
</html>
