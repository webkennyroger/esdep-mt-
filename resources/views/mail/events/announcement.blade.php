<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reservation->event_name }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f8f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f8f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #003f32; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">ESDEP-MT</h1>
                            <p style="margin: 8px 0 0; color: #8aa39a; font-size: 13px;">Escola Superior da Defensoria Publica do Estado de Mato Grosso</p>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding: 30px 40px 10px;">
                            <p style="margin: 0; color: #003f32; font-size: 16px;">Ola, <strong>{{ $recipientName }}</strong>,</p>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 10px 40px;">
                            <p style="margin: 0 0 20px; color: #334155; font-size: 15px; line-height: 1.6;">
                                {{ $customMessage ?? 'Voce esta recebendo este comunicado sobre um proximo evento da ESDEP-MT.' }}
                            </p>
                        </td>
                    </tr>

                    {{-- Event Card --}}
                    <tr>
                        <td style="padding: 10px 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0faf5; border-radius: 10px; border: 1px solid #d8e2dd;">
                                <tr>
                                    <td style="padding: 25px 30px;">
                                        <h2 style="margin: 0 0 15px; color: #003f32; font-size: 20px; font-weight: 700;">{{ $reservation->event_name }}</h2>

                                        <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 6px 0; color: #00664f; font-size: 14px; width: 30px; vertical-align: top;">&#128197;</td>
                                                <td style="padding: 6px 0; color: #334155; font-size: 14px;">
                                                    {{ \Carbon\Carbon::parse($reservation->date)->locale('pt_BR')->translatedFormat('d \\d\\e F \\d\\e Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #00664f; font-size: 14px; width: 30px; vertical-align: top;">&#128336;</td>
                                                <td style="padding: 6px 0; color: #334155; font-size: 14px;">
                                                    {{ $reservation->start_time }} as {{ $reservation->end_time }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #00664f; font-size: 14px; width: 30px; vertical-align: top;">&#127970;</td>
                                                <td style="padding: 6px 0; color: #334155; font-size: 14px;">
                                                    {{ $reservation->room?->name ?? 'Sala a definir' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #00664f; font-size: 14px; width: 30px; vertical-align: top;">&#128101;</td>
                                                <td style="padding: 6px 0; color: #334155; font-size: 14px;">
                                                    {{ $reservation->attendees_count }} participantes
                                                </td>
                                            </tr>
                                        </table>

                                        @if($reservation->coffee_break || $reservation->sonorizacao || $reservation->transmissao)
                                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #d8e2dd;">
                                            <p style="margin: 0 0 8px; color: #00664f; font-size: 13px; font-weight: 600;">Servicos:</p>
                                            @if($reservation->coffee_break)
                                                <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 3px 10px; border-radius: 12px; font-size: 12px; margin-right: 5px;">Coffee Break</span>
                                            @endif
                                            @if($reservation->sonorizacao)
                                                <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 3px 10px; border-radius: 12px; font-size: 12px; margin-right: 5px;">Sonorizacao</span>
                                            @endif
                                            @if($reservation->transmissao)
                                                <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 3px 10px; border-radius: 12px; font-size: 12px;">Transmissao</span>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8faf9; padding: 25px 40px; border-top: 1px solid #d8e2dd; text-align: center;">
                            <p style="margin: 0 0 5px; color: #64748b; font-size: 12px;">
                                ESDEP-MT - Escola Superior da Defensoria Publica do Estado de Mato Grosso
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
