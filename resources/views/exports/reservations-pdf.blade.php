<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #1e293b; }
        h1 { font-size: 16px; margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #cbd5e1; padding: 4px 5px; text-align: left; word-wrap: break-word; overflow-wrap: break-word; }
        th { background-color: #f0faf5; font-size: 8px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>Reservas — ESDEP-MT</h1>
    <table>
        <colgroup>
            <col style="width: 7%">
            <col style="width: 14%">
            <col style="width: 8%">
            <col style="width: 6%">
            <col style="width: 7%">
            <col style="width: 10%">
            <col style="width: 13%">
            <col style="width: 5%">
            <col style="width: 6%">
            <col style="width: 6%">
            <col style="width: 6%">
            <col style="width: 6%">
            <col style="width: 6%">
        </colgroup>
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
