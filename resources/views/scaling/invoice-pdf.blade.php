<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoiceNumber }} - RMD Corporation</title>
    <style>
        @page {
            size: letter portrait;
            margin: 15px;
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
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            padding: 10px 0 6px;
        }

        .logo {
            width: 72px;
            height: 72px;
            margin: 0 auto 8px;
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
            font-size: 10px;
            color: #475569;
            margin: 6px auto 0;
            max-width: 520px;
            line-height: 1.35;
        }

        .details {
            width: 100%;
            margin-top: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            border-collapse: collapse;
        }

        .details td {
            padding: 4px 8px;
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
            margin-top: 14px;
            font-size: 10px;
        }

        .breakdown th,
        .breakdown td {
            border: 1px solid #e2e8f0;
            padding: 6px 12px;
            font-size: 9px;
        }

        .breakdown th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .breakdown td:first-child,
        .breakdown th:first-child {
            text-align: left;
            padding-left: 10px;
        }

        .breakdown td:nth-child(n+2),
        .breakdown th:nth-child(n+2) {
            text-align: right;
            padding-right: 15px;
        }

        .breakdown tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .breakdown tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .breakdown tfoot tr {
            background: #f1f5f9;
            font-weight: 700;
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
            margin-top: 12px;
            border-collapse: collapse;
        }

        .summary-left,
        .summary-right {
            display: table-cell;
            vertical-align: top;
            padding: 0;
        }

        .summary-left {
            padding-right: 8px;
            width: 60%;
        }

        .summary-right {
            width: 40%;
        }

        .summary-box .row,
        .summary-highlight .row {
            margin-bottom: 6px;
        }

        .summary-box,
        .summary-highlight {
            border-radius: 14px;
            padding: 8px 12px;
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

            @php
                $total_pieces = array_sum(array_column($breakdownBrackets, 'pieces'));
                $total_volume = array_sum(array_column($breakdownBrackets, 'total_volume'));
                $gross_wood_amount = $calculatedGrossAmount;
            @endphp

            <table style="width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0">
                <thead>
                    <tr style="background-color: #f8fafc;">
                        <th style="width: 30%; text-align: left; padding: 8px;">SIZE / GRADE BRACKET</th>
                        <th style="width: 15%; text-align: center; padding: 8px;">PIECES</th>
                        <th style="width: 20%; text-align: center; padding: 8px;">TOTAL VOLUME (M³)</th>
                        <th style="width: 17%; text-align: right; padding: 8px;">RATE (₱/M³)</th>
                        <th style="width: 18%; text-align: right; padding: 8px;">SUBTOTAL (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($breakdownBrackets as $bracket)
                        <tr>
                            <td style="text-align: left; padding: 8px; font-weight: 700;">{{ $bracket['bracket'] }}</td>
                            <td style="text-align: center; padding: 8px;">{{ number_format($bracket['pieces']) }}</td>
                            <td style="text-align: center; padding: 8px; font-weight: 700;">{{ number_format($bracket['total_volume'], 3) }}</td>
                            <td style="text-align: right; padding: 8px;">{{ $bracket['rate'] > 0 ? '₱ ' . number_format($bracket['rate'], 2) : '-' }}</td>
                            <td style="text-align: right; padding: 8px; font-weight: 700;">₱ {{ number_format($bracket['subtotal'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <!-- TOTAL ROW FIX -->
                    <tr style="font-weight: bold; background-color: #f8fafc; border-top: 2px solid #000;">
                        <td align="left" style="width: 30%; text-align: left !important; padding: 8px 10px; font-weight: bold;">
                            TOTAL
                        </td>
                        <td align="center" style="width: 15%; text-align: center !important; padding: 8px; font-weight: bold;">
                            {{ number_format($total_pieces) }}
                        </td>
                        <td align="center" style="width: 20%; text-align: center !important; padding: 8px; font-weight: bold;">
                            {{ number_format($total_volume, 3) }}
                        </td>
                        <td align="right" style="width: 17%; text-align: right !important; padding: 8px 10px; font-weight: bold;">
                            -
                        </td>
                        <td align="right" style="width: 18%; text-align: right !important; padding: 8px 10px; font-weight: bold;">
                            ₱ {{ number_format($gross_wood_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <table style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-top: 10px;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; min-height: 190px; box-sizing: border-box;">
                            <div style="font-size: 10px; font-weight: 800; margin-bottom: 10px;">DEDUCTIONS BREAKDOWN</div>
                            <div style="display: table; width: 100%; margin-bottom: 6px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">Add Driver's Assistance</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700; font-family: monospace, sans-serif; color: #0f172a;">+ ₱ {{ number_format($truckLoad->drivers_assistance, 2) }}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 6px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">Expenses Deduction</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700; font-family: monospace, sans-serif;">- ₱ {{ number_format($truckLoad->expenses_deduction, 2) }}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 6px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">Travel Paper / Permit</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700; font-family: monospace, sans-serif;">- ₱ {{ number_format($truckLoad->travel_paper_deduction, 2) }}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 6px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">Trucking Deduction</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700; font-family: monospace, sans-serif;">- ₱ {{ number_format($truckLoad->trucking_deduction, 2) }}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 8px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">Cash Advance</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700; font-family: monospace, sans-serif;">- ₱ {{ number_format($truckLoad->cash_advance, 2) }}</span>
                            </div>
                            <div style="border-top: 1px solid #cbd5e1; margin: 6px 0;"></div>
                            <div style="display: table; width: 100%; margin-top: 6px;">
                                <span style="display: table-cell; text-align: left; font-weight: 800; color: #334155;">TOTAL DEDUCTIONS</span>
                                <span style="display: table-cell; text-align: right; font-weight: 800; font-family: monospace, sans-serif; color: #0f172a;">- ₱ {{ number_format($truckLoad->total_deductions, 2) }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; min-height: 190px; box-sizing: border-box;">
                            <div style="font-size: 10px; font-weight: 800; margin-bottom: 10px;">FINANCIAL SUMMARY</div>
                            <div style="display: table; width: 100%; margin-bottom: 6px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">GROSS WOOD AMOUNT</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700;">₱ {{ number_format($gross_wood_amount, 2) }}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 6px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">ADD DRIVER'S ASSISTANCE</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700;">+ ₱ {{ number_format($truckLoad->drivers_assistance, 2) }}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 8px;">
                                <span style="display: table-cell; text-align: left; color: #475569;">LESS TOTAL DEDUCTIONS</span>
                                <span style="display: table-cell; text-align: right; font-weight: 700;">- ₱ {{ number_format($truckLoad->total_deductions, 2) }}</span>
                            </div>
                            <div style="border-top: 1px solid #cbd5e1; margin: 6px 0;"></div>
                            <div style="display: table; width: 100%; margin-top: 8px;">
                                <span style="display: table-cell; text-align: left; font-weight: 800; color: #0f172a;">NET AMOUNT PAYABLE TO SUPPLIER</span>
                                <span style="display: table-cell; text-align: right; font-weight: 900; font-size: 18px; color: #0f172a;">₱ {{ number_format($truckLoad->net_payable, 2) }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="signatures" cellpadding="0" cellspacing="0">
                <div class="summary-left">
                    <div class="summary-box">
                        <div class="row">
                            <span class="label">Driver's Assistance</span>
                            <span class="value">+ ₱ {{ number_format($truckLoad->drivers_assistance, 2) }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Expenses Deduction</span>
                            <span class="value">- ₱ {{ number_format($truckLoad->expenses_deduction, 2) }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Travel Paper</span>
                            <span class="value">- ₱ {{ number_format($truckLoad->travel_paper_deduction, 2) }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Trucking Deduction</span>
                            <span class="value">- ₱ {{ number_format($truckLoad->trucking_deduction, 2) }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Cash Advance</span>
                            <span class="value">- ₱ {{ number_format($truckLoad->cash_advance, 2) }}</span>
                        </div>
                        <div class="row" style="border-top:1px solid #cbd5e1; margin-top:6px; padding-top:6px;">
                            <span class="label">Total Deductions</span>
                            <span class="value">- ₱ {{ number_format($truckLoad->total_deductions, 2) }}</span>
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
