<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservas — ESDEP-MT</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #f0faf5; font-size: 11px; text-transform: uppercase; }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <h1>Reservas — ESDEP-MT</h1>
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
