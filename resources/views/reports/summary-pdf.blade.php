<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging & Wood Scaling Summary Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 11px;
            margin: 0;
            background: #ffffff;
        }

        .page {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .center {
            text-align: center;
        }

        .logo {
            display: block;
            margin: 0 auto 10px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        .brand-tag {
            font-size: 9px;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            font-weight: 800;
            color: #d97706;
            margin: 0 0 8px;
        }

        .report-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            color: #0f172a;
        }

        .report-subtitle {
            margin: 8px auto 0;
            color: #475569;
            font-size: 11px;
            line-height: 1.4;
            max-width: 540px;
        }

        .details {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            margin: 22px 0;
            border-collapse: collapse;
        }

        .details td {
            padding: 10px 12px;
            vertical-align: top;
            font-size: 10px;
            color: #0f172a;
        }

        .details .section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #334155;
            padding-bottom: 8px;
        }

        .details .label {
            font-size: 8.8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
        }

        .details .value {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }

        .details td + td {
            border-left: 1px solid #e2e8f0;
        }

        .table-wrap {
            width: 100%;
            overflow: hidden;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 14px;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
            vertical-align: middle;
        }

        table.report-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 9px;
        }

        table.report-table td {
            color: #0f172a;
        }

        table.report-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        table.report-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .monospace {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .summary-section {
            width: 100%;
            margin-top: 18px;
            display: table;
            border-collapse: collapse;
        }

        .summary-left,
        .summary-right {
            display: table-cell;
            vertical-align: top;
            padding: 0;
        }

        .summary-left {
            padding-right: 12px;
            width: 60%;
        }

        .summary-right {
            width: 40%;
        }

        .summary-box {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #ffffff;
            padding: 16px;
        }

        .summary-box strong {
            display: block;
            margin-bottom: 6px;
            color: #0f172a;
            font-size: 11px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-row span {
            display: table-cell;
            vertical-align: middle;
            font-size: 10px;
        }

        .summary-row .label {
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .summary-row .value {
            text-align: right;
            font-weight: 800;
            color: #0f172a;
        }

        .summary-highlight {
            border: 2px solid #16a34a;
            border-radius: 12px;
            background: #ecfdf5;
            padding: 16px;
            margin-top: 0;
        }

        .summary-highlight .label {
            font-size: 9px;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
            display: block;
        }

        .summary-highlight .amount {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .signatures {
            width: 100%;
            margin-top: 28px;
            border-collapse: collapse;
        }

        .signature-cell {
            width: 33%;
            padding-top: 28px;
            text-align: center;
            font-size: 10px;
            color: #334155;
        }

        .signature-line {
            display: block;
            border-bottom: 1px solid #cbd5e1;
            width: 78%;
            margin: 0 auto 8px;
            padding-bottom: 6px;
        }

        .signature-title {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f172a;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp
    <div class="page">
        <div class="center">
            @if($logoData)
                <img src="data:image/png;base64,{{ $logoData }}" class="logo" alt="RMD Logo">
            @elseif(file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo" alt="RMD Logo">
            @else
                <div style="font-weight: bold; font-size: 16px; margin-bottom: 8px;">RMD CORP LOGO</div>
            @endif
            <p class="brand-tag">R M D C O R P O R A T I O N</p>
            <h1 class="report-title">LOGGING & WOOD SCALING SUMMARY REPORT</h1>
            <p class="report-subtitle">Taguibo, Butuan City • Official Summary Payout & Scaling Verification</p>
        </div>

        <table class="details" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="section-title">Summary Metadata</div>
                    <div class="detail-line">
                        <div class="label">Report Type</div>
                        <div class="value">{{ $reportType }}</div>
                    </div>
                    <div class="detail-line">
                        <div class="label">Total Scale Sheets</div>
                        <div class="value">{{ number_format(count($reportRows)) }}</div>
                    </div>
                    <div class="detail-line">
                        <div class="label">Generated By</div>
                        <div class="value">{{ $generatedBy }}</div>
                    </div>
                </td>
                <td>
                    <div class="section-title">Report Metadata</div>
                    <div class="detail-line">
                        <div class="label">Period Covered</div>
                        <div class="value">{{ $periodLabel }}</div>
                    </div>
                    <div class="detail-line">
                        <div class="label">Date Generated</div>
                        <div class="value">{{ $dateGenerated }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="table-wrap">
            <table class="report-table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>SHEET NO.</th>
                        <th>SUPPLIER NAME</th>
                        <th>TRUCK PLATE</th>
                        <th>SCALED DATE</th>
                        <th class="text-right">TOTAL LOGS</th>
                        <th class="text-right">TOTAL VOLUME (M³)</th>
                        <th class="text-right">NET PAYOUT (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportRows as $row)
                        <tr>
                            <td class="monospace">{{ $row['sheet_no'] }}</td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td class="monospace">{{ $row['truck_plate'] }}</td>
                            <td>{{ $row['scaled_date'] }}</td>
                            <td class="text-right monospace">{{ number_format($row['total_logs']) }}</td>
                            <td class="text-right monospace">{{ number_format($row['total_volume'], 3) }}</td>
                            <td class="text-right monospace">₱ {{ number_format($row['net_payout'], 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary-section">
            <div class="summary-left">
                <div class="summary-box">
                    <strong>Totals</strong>
                    <div class="summary-row"><span class="label">Total Logs Scaled</span><span class="value">{{ number_format($grandTotals['total_logs']) }} pcs</span></div>
                    <div class="summary-row"><span class="label">Total Combined Volume</span><span class="value">{{ number_format($grandTotals['total_volume'], 3) }} m³</span></div>
                </div>
            </div>
            <div class="summary-right">
                <div class="summary-highlight">
                    <span class="label">NET TOTAL SUPPLIER PAYOUT</span>
                    <span class="amount">₱ {{ number_format($grandTotals['net'], 3) }}</span>
                </div>
            </div>
        </div>

        <table class="signatures" cellpadding="0" cellspacing="0">
            <tr>
                <td class="signature-cell">
                    <span class="signature-line"></span>
                    <span class="signature-title">Prepared By</span>
                    <div>Scaler Staff</div>
                </td>
                <td class="signature-cell">
                    <span class="signature-line"></span>
                    <span class="signature-title">Reviewed By</span>
                    <div>Accountant</div>
                </td>
                <td class="signature-cell">
                    <span class="signature-line"></span>
                    <span class="signature-title">Approved By</span>
                    <div>RMD Management</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
