<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Rejeitada - {{ $reservation->event_name }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f8f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f8f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626, #ef4444); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">{{ $reservation->event_name }}</h1>
                            <div style="margin-top: 10px;">
                                <span style="display: inline-block; background-color: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; color: #ffffff; font-size: 12px; font-weight: 600;">Rejeitada</span>
                            </div>
                        </td>
                    </tr>

                    {{-- Mensagem de Rejeicao --}}
                    <tr>
                        <td style="padding: 25px 40px 10px;">
                            <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 15px 20px; border-radius: 0 8px 8px 0;">
                                <p style="margin: 0; color: #991b1b; font-size: 14px; line-height: 1.6;">
                                    <strong>Sua solicitacao de reserva foi rejeitada.</strong>
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding: 20px 40px 15px;">
                            <p style="margin: 0; color: #003f32; font-size: 15px;">Ola, <strong>{{ $reservation->requester_name }}</strong>,</p>
                            <p style="margin: 8px 0 0; color: #64748b; font-size: 13px;">Segue abaixo as informacoes da reserva rejeitada:</p>
                        </td>
                    </tr>

                    {{-- Info Grid --}}
                    <tr>
                        <td style="padding: 10px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    {{-- Data e Horario --}}
                                    <td width="50%" style="padding: 0 5px 10px 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8faf9; border: 1px solid #e2e8f0; border-radius: 10px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <p style="margin: 0 0 8px; color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">&#128336; Data e Horario</p>
                                                    <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 600;">{{ \Carbon\Carbon::parse($reservation->date)->locale('pt_BR')->translatedFormat('d \\d\\e F \\d\\e Y') }}</p>
                                                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">{{ $reservation->start_time }} - {{ $reservation->end_time }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    {{-- Sala --}}
                                    <td width="50%" style="padding: 0 0 10px 5px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8faf9; border: 1px solid #e2e8f0; border-radius: 10px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <p style="margin: 0 0 8px; color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">&#127970; Sala</p>
                                                    <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 600;">{{ $reservation->room?->name ?? 'Nao informada' }}</p>
                                                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Capacidade: {{ $reservation->room?->capacity ?? '-' }} pessoas</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    {{-- Solicitante --}}
                                    <td width="50%" style="padding: 0 5px 10px 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8faf9; border: 1px solid #e2e8f0; border-radius: 10px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <p style="margin: 0 0 8px; color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">&#128100; Solicitante</p>
                                                    <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 600;">{{ $reservation->requester_name }}</p>
                                                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">{{ $reservation->requester_email }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    {{-- Publico --}}
                                    <td width="50%" style="padding: 0 0 10px 5px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8faf9; border: 1px solid #e2e8f0; border-radius: 10px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <p style="margin: 0 0 8px; color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">&#128101; Publico Estimado</p>
                                                    <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 600;">{{ $reservation->attendees_count }} pessoas</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Servicos --}}
                    <tr>
                        <td style="padding: 5px 40px 20px;">
                            <p style="margin: 0 0 10px; color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Servicos Solicitados</p>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    @if($reservation->coffee_break)
                                        <td style="padding: 0 6px 6px 0;">
                                            <span style="display: inline-block; background-color: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">&#9749; Coffee Break</span>
                                        </td>
                                    @endif
                                    @if($reservation->sonorizacao)
                                        <td style="padding: 0 6px 6px 0;">
                                            <span style="display: inline-block; background-color: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">&#128266; Sonorizacao</span>
                                        </td>
                                    @endif
                                    @if($reservation->transmissao)
                                        <td style="padding: 0 6px 6px 0;">
                                            <span style="display: inline-block; background-color: #f3e8ff; color: #7c3aed; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">&#127909; Transmissao</span>
                                        </td>
                                    @endif
                                    @if($reservation->projetor)
                                        <td style="padding: 0 6px 6px 0;">
                                            <span style="display: inline-block; background-color: #cffafe; color: #0891b2; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">&#128187; Projetor</span>
                                        </td>
                                    @endif
                                    @if(!$reservation->coffee_break && !$reservation->sonorizacao && !$reservation->transmissao && !$reservation->projetor)
                                        <td>
                                            <span style="color: #94a3b8; font-size: 13px; font-style: italic;">Nenhum servico solicitado</span>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Descricao --}}
                    @if($reservation->description)
                    <tr>
                        <td style="padding: 0 40px 20px;">
                            <p style="margin: 0 0 10px; color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Descrição do solicitante</p>
                            <div style="background-color: #f8faf9; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px;">
                                <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">{{ $reservation->description }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    {{-- Motivo da Rejeicao --}}
                    @if($reservation->reject_reason)
                    <tr>
                        <td style="padding: 0 40px 20px;">
                            <p style="margin: 0 0 10px; color: #dc2626; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">&#10060; Motivo da Rejeicao</p>
                            <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 15px;">
                                <p style="margin: 0; color: #991b1b; font-size: 13px; line-height: 1.6;">{{ $reservation->reject_reason }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    {{-- Protocolo --}}
                    <tr>
                        <td style="padding: 0 40px 25px;">
                            <div style="background-color: #f0faf5; border: 1px solid #d8e2dd; border-radius: 10px; padding: 15px; text-align: center;">
                                <p style="margin: 0 0 5px; color: #64748b; font-size: 12px;">Protocolo da Reserva</p>
                                <p style="margin: 0; color: #059669; font-size: 18px; font-weight: 700; letter-spacing: 0.05em;">{{ $reservation->protocol }}</p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8faf9; padding: 20px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 5px; color: #64748b; font-size: 12px;">
                                ESDEP-MT - Escola Superior da Defensoria Publica do Estado de Mato Grosso
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 11px;">
                                Sistema de Reserva de Espacos - Duvidas? Entre em contato com a administracao.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
