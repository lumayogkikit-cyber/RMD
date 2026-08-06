<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoiceNumber }} - RMD Corporation</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 0;
            background: white;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
        }

        .paper {
            width: 100%;
            padding: 0;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            padding: 16px 0 10px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            border-radius: 9999px;
            object-fit: cover;
        }

        .brand-tag {
            font-size: 9px;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            font-weight: 800;
            color: #d97706;
            margin-bottom: 8px;
        }

        .title {
            font-size: 24px;
            font-weight: 900;
            margin: 0;
        }

        .subtitle {
            font-size: 11px;
            color: #475569;
            margin: 8px auto 0;
            max-width: 520px;
            line-height: 1.4;
        }

        .details {
            width: 100%;
            margin-top: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            border-collapse: collapse;
        }

        .details td {
            padding: 10px 12px;
            vertical-align: top;
            font-size: 10px;
        }

        .details .left,
        .details .right {
            width: 50%;
        }

        .detail-label {
            font-size: 8px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
            display: block;
        }

        .detail-value {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
            display: block;
        }

        .breakdown {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }

        .breakdown th,
        .breakdown td {
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
        }

        .breakdown th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 9px;
        }

        .breakdown tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .breakdown tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .monospace {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .summary {
            display: table;
            width: 100%;
            margin-top: 18px;
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

        .summary-box,
        .summary-highlight {
            border-radius: 14px;
            padding: 16px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
        }

        .summary-box .row,
        .summary-highlight .row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .summary-box .label,
        .summary-highlight .label {
            display: table-cell;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .summary-box .value,
        .summary-highlight .value {
            display: table-cell;
            text-align: right;
            font-weight: 800;
            color: #0f172a;
        }

        .summary-highlight {
            border-color: #16a34a;
            background: #ecfdf5;
        }

        .summary-highlight .title {
            display: block;
            font-size: 9px;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .summary-highlight .amount {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .signatures {
            width: 100%;
            margin-top: 26px;
            border-collapse: collapse;
        }

        .signature-cell {
            width: 33.333%;
            text-align: center;
            font-size: 10px;
            color: #475569;
        }

        .signature-line {
            display: block;
            width: 75%;
            margin: 0 auto 10px;
            border-bottom: 1px solid #cbd5e1;
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
        $logoPath = 'file:///' . str_replace('\\', '/', public_path('images/logo.png'));
    @endphp
    <div class="container">
        <div class="paper">
            <div class="header">
                <img src="{{ $logoPath }}" class="logo" alt="RMD Logo">
                <p class="brand-tag">R M D C O R P O R A T I O N</p>
                <h1 class="title">Wood Scaling Invoice</h1>
                <p class="subtitle">Official invoice for wood scaling, supplier payout and verification.</p>
            </div>

            <table class="details" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="left">
                        <span class="detail-label">Supplier Name</span>
                        <span class="detail-value">{{ $supplierName }}</span>
                    </td>
                    <td class="right">
                        <span class="detail-label">Invoice No</span>
                        <span class="detail-value monospace">{{ $invoiceNumber }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="left">
                        <span class="detail-label">Truck Plate No</span>
                        <span class="detail-value monospace">{{ $truckPlate }}</span>
                    </td>
                    <td class="right">
                        <span class="detail-label">Scale Sheet No</span>
                        <span class="detail-value monospace">{{ $truckLoad->scale_sheet_no }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="left">
                        <span class="detail-label">Scaled By</span>
                        <span class="detail-value">{{ $truckLoad->scaled_by }}</span>
                    </td>
                    <td class="right">
                        <span class="detail-label">Prepared On</span>
                        <span class="detail-value">{{ $preparedOn }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="left">
                        <span class="detail-label">Date Unloaded</span>
                        <span class="detail-value">{{ optional($truckLoad->date_unload)->format('M d, Y') ?? '-' }}</span>
                    </td>
                    <td class="right">
                        <span class="detail-label">Date Scaled</span>
                        <span class="detail-value">{{ optional($truckLoad->date_scaled)->format('M d, Y') ?? '-' }}</span>
                    </td>
                </tr>
            </table>

            <table class="breakdown" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>SIZE / GRADE BRACKET</th>
                        <th class="text-right">PIECES</th>
                        <th class="text-right">TOTAL VOLUME (M³)</th>
                        <th class="text-right">RATE (₱/M³)</th>
                        <th class="text-right">SUBTOTAL (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($breakdownBrackets as $bracket)
                        <tr>
                            <td class="monospace">{{ $bracket['bracket'] }}</td>
                            <td class="text-right monospace">{{ number_format($bracket['pieces']) }}</td>
                            <td class="text-right monospace">{{ number_format($bracket['total_volume'], 3) }}</td>
                            <td class="text-right monospace">{{ $bracket['rate'] > 0 ? '₱ ' . number_format($bracket['rate'], 2) : '-' }}</td>
                            <td class="text-right monospace">₱ {{ number_format(($bracket['pieces'] ?? 0) > 0 ? $bracket['subtotal'] : 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-left">
                    <div class="summary-box">
                        <div class="row">
                            <span class="label">Gross Wood Amount</span>
                            <span class="value">₱ {{ number_format($truckLoad->gross_amount, 2) }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Total Deductions</span>
                            <span class="value">₱ {{ number_format($truckLoad->total_deductions, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="summary-right">
                    <div class="summary-highlight">
                        <span class="title">Net Amount Payable to Supplier</span>
                        <span class="amount">₱ {{ number_format($truckLoad->net_payable, 2) }}</span>
                    </div>
                </div>
            </div>

            <table class="signatures" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="signature-cell">
                        <span class="signature-line"></span>
                        <span class="signature-title">Prepared By</span>
                        <div>{{ $truckLoad->scaled_by }}</div>
                    </td>
                    <td class="signature-cell">
                        <span class="signature-line"></span>
                        <span class="signature-title">Reviewed By</span>
                        <div>Supplier / Driver</div>
                    </td>
                    <td class="signature-cell">
                        <span class="signature-line"></span>
                        <span class="signature-title">Approved By</span>
                        <div>RMD Management</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
