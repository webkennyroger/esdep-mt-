<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Solicitação de Reserva</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; color: #333; }
        .info-box { background-color: #eff6ff; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info-box p { margin: 5px 0; }
        .label { font-weight: bold; color: #1e40af; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nova Solicitação</h1>
        </div>
        <div class="content">
            <p>Uma nova solicitação de reserva foi registrada no sistema.</p>
            
            <div class="info-box">
                <p><span class="label">Evento:</span> {{ $reservation->event_name }}</p>
                <p><span class="label">Solicitante:</span> {{ $reservation->requester_name }}</p>
                <p><span class="label">Email:</span> {{ $reservation->requester_email }}</p>
                <p><span class="label">Sala:</span> {{ $reservation->room->name }}</p>
                <p><span class="label">Data:</span> {{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y') }}</p>
                <p><span class="label">Horário:</span> {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</p>
                <p><span class="label">Participantes:</span> {{ $reservation->attendees_count }}</p>
            </div>

            @if($reservation->description)
            <p><span class="label">Descrição do solicitante:</span></p>
            <div class="info-box">
                <p>{{ $reservation->description }}</p>
            </div>
            @endif

            <p>Acesse o painel administrativo para autorizar ou rejeitar esta solicitação.</p>
            <p>Atenciosamente,<br><strong>Equipe ESDEP-MT</strong></p>
        </div>
        <div class="footer">
            <p>Escola Superior da Defensoria Pública do Estado de Mato Grosso</p>
        </div>
    </div>
</body>
</html>
