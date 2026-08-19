<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembrete de Reserva</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #f59e0b; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; color: #333; }
        .info-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info-box p { margin: 5px 0; }
        .label { font-weight: bold; color: #1e40af; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Lembrete de Reserva</h1>
        </div>
        <div class="content">
            <p>Prezado(a) <strong>{{ $reservation->requester_name }}</strong>,</p>
            <p>Este e-mail e um lembrete de que voce possui uma reserva <strong style="color: #16a34a;">APROVADA</strong> para amanha!</p>
            
            <div class="info-box">
                <p><span class="label">Evento:</span> {{ $reservation->event_name }}</p>
                <p><span class="label">Sala:</span> {{ $reservation->room->name }}</p>
                <p><span class="label">Data:</span> {{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y') }}</p>
                <p><span class="label">Horario:</span> {{ $reservation->start_time }} - {{ $reservation->end_time }}</p>
                <p><span class="label">Participantes:</span> {{ $reservation->attendees_count }}</p>
            </div>
            
            <p>Por favor, chegue com antecedencia e respeite o horario agendado.</p>
            <p>Atenciosamente,<br><strong>Equipe ESDEP-MT</strong></p>
        </div>
        <div class="footer">
            <p>Escola Superior da Defensoria Publica do Estado de Mato Grosso</p>
        </div>
    </div>
</body>
</html>
