<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $truckLoad->invoice_no }} - RMD Corp</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* HEADER SECTION */
        .logo {
            width: 70px;
            height: 70px;
            display: block;
            margin: 0 auto 5px auto;
        }
        .company-sub {
            color: #d97706;
            letter-spacing: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 800;
            margin: 4px 0;
            color: #0f172a;
        }
        .header-subtext {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 12px;
        }
        .divider {
            border-bottom: 2px solid #1e293b;
            margin-bottom: 15px;
        }

        /* METADATA BOX (2-COLUMN TABLE) */
        .meta-card {
            border: 1.5px solid #334155;
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 15px;
            width: 100%;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 10px;
            vertical-align: middle;
        }
        .label {
            color: #475569;
            font-weight: bold;
            font-size: 9px;
        }
        .val {
            font-weight: bold;
            color: #0f172a;
        }

        /* BREAKDOWN TABLES */
        .section-header {
            font-size: 11px;
            font-weight: 800;
            margin: 12px 0 6px 0;
            letter-spacing: 0.5px;
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1.5px solid #334155;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 9.5px;
            font-weight: bold;
            padding: 8px 10px;
            border-bottom: 1.5px solid #334155;
            border-right: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .data-table th:last-child {
            border-right: none;
        }
        .data-table td {
            padding: 7px 10px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .data-table td:last-child {
            border-right: none;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* TOTALS & SIGNATURES */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .payout-box {
            border: 2px solid #22c55e;
            border-radius: 12px;
            background-color: #f0fdf4;
            padding: 12px;
            text-align: center;
        }
        .payout-label {
            color: #15803d;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .payout-amount {
            color: #14532d;
            font-size: 20px;
            font-weight: bold;
            margin-top: 2px;
        }
        .sig-table {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
        }
        .sig-line {
            border-top: 1px dashed #94a3b8;
            width: 80%;
            margin: 0 auto 4px auto;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- HEADER -->
    <div class="text-center">
        @if($logoData)
            <img src="data:image/png;base64,{{ $logoData }}" style="width: 75px; height: 75px; margin: 0 auto 6px auto; display: block;" alt="RMD Logo">
        @elseif(file_exists($logoPath))
            <img src="{{ $logoPath }}" style="width: 75px; height: 75px; margin: 0 auto 6px auto; display: block;" alt="RMD Logo">
        @else
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 5px;">RMD CORP LOGO</div>
        @endif
        <div class="company-sub">R M D   C O R P O R A T I O N</div>
        <div class="invoice-title">Wood Scaling Invoice</div>
        <div class="header-subtext">Taguibo, Butuan City • Official invoice for wood scaling, supplier payout and verification.</div>
    </div>

    <div class="divider"></div>

    <!-- METADATA CARD -->
    <div class="meta-card">
        <table class="meta-table">
            <tr>
                <td style="width: 20%;" class="label">SUPPLIER NAME:</td>
                <td style="width: 30%; font-size: 11px;" class="val">{{ $truckLoad->supplier->name ?? 'N/A' }}</td>
                <td style="width: 20%;" class="label">INVOICE NO:</td>
                <td style="width: 30%;" class="val text-right">{{ $truckLoad->invoice_no }}</td>
            </tr>
            <tr>
                <td class="label">TRUCK PLATE NO:</td>
                <td class="val">{{ $truckLoad->truck_plate_no }}</td>
                <td class="label">SCALE SHEET NO:</td>
                <td class="val text-right">{{ $truckLoad->scale_sheet_no }}</td>
            </tr>
            <tr>
                <td class="label">SCALED BY:</td>
                <td class="val">{{ $truckLoad->scaled_by ?? 'Scaler Staff' }}</td>
                <td class="label">DATE UNLOADED:</td>
                <td class="val text-right">{{ \Carbon\Carbon::parse($truckLoad->date_unload)->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">PREPARED ON:</td>
                <td class="val">{{ \Carbon\Carbon::parse($truckLoad->created_at)->format('M d, Y') }}</td>
                <td class="label">DATE SCALED:</td>
                <td class="val text-right">{{ \Carbon\Carbon::parse($truckLoad->date_scaled)->format('M d, Y') }}</td>
            </tr>
        </table>
    </div>

    @php
        $total_pieces = array_sum(array_column($breakdownBrackets, 'pieces'));
        $total_volume = array_sum(array_column($breakdownBrackets, 'total_volume'));
        $gross_wood_amount = $calculatedGrossAmount;
        $driver_assistance = $truckLoad->drivers_assistance;
        $expenses_deduction = $truckLoad->expenses_deduction;
        $travel_paper = $truckLoad->travel_paper_deduction;
        $trucking_deduction = $truckLoad->trucking_deduction;
        $cash_advance = $truckLoad->cash_advance;
        $other_deduction_label = trim($truckLoad->other_deduction_label ?: 'Other Deduction');
        $other_deduction_amount = (float) $truckLoad->other_deduction_amount;
        $total_deductions = $calculatedDeductions;
        $net_payable = $calculatedNetPayable;
    @endphp

    <!-- WOOD SCALING BREAKDOWN TABLE -->
    <div class="section-header">WOOD SCALING BREAKDOWN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%; text-align: left;">SIZE / GRADE BRACKET</th>
                <th style="width: 15%;" class="text-center">PIECES</th>
                <th style="width: 20%;" class="text-center">TOTAL VOLUME (M³)</th>
                <th style="width: 17%;" class="text-right">RATE (₱/M³)</th>
                <th style="width: 18%;" class="text-right">SUBTOTAL (₱)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($breakdownBrackets as $bracket)
                <tr>
                    <td class="font-bold">{{ $bracket['bracket'] }}</td>
                    <td class="text-center">{{ number_format($bracket['pieces']) }}</td>
                    <td class="text-center font-bold">{{ number_format($bracket['total_volume'], 3) }}</td>
                    <td class="text-right">{{ $bracket['rate'] > 0 ? '₱ ' . number_format($bracket['rate'], 2) : '-' }}</td>
                    <td class="text-right font-bold">₱ {{ number_format($bracket['subtotal'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No scaling records found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f8fafc; border-top: 2px solid #000;">
                <td>TOTAL</td>
                <td>{{ $total_pieces }}</td>
                <td>{{ number_format($total_volume, 3) }}</td>
                <td>-</td>
                <td>₱ {{ number_format($gross_wood_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- TOTALS & PAYOUT SECTION -->
    <table style="width: 100%; border: none; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 8px; border: none;">
                <table style="width:100%; border:none; border-collapse: collapse;">
                    <tr>
                        <td style="border:1px solid #334155; border-radius:10px; padding:10px; vertical-align: top;">
                            <div style="font-size: 10px; font-weight: 800; margin-bottom: 8px;">DEDUCTIONS BREAKDOWN</div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 4px;">Add Driver's Assistance: <span style="float: right; color: #15803d;">+ ₱ {{ number_format($driver_assistance, 2) }}</span></div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 4px;">Expenses Deduction: <span style="float: right;">- ₱ {{ number_format($expenses_deduction, 2) }}</span></div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 4px;">Travel Paper / Permit: <span style="float: right;">- ₱ {{ number_format($travel_paper, 2) }}</span></div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 4px;">Trucking Deduction: <span style="float: right;">- ₱ {{ number_format($trucking_deduction, 2) }}</span></div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 6px;">Cash Advance: <span style="float: right;">- ₱ {{ number_format($cash_advance, 2) }}</span></div>
                            @if(($other_deduction_amount ?? 0) > 0)
                                <div style="font-size: 9.5px; color: #475569; margin-bottom: 6px;">Less {{ $other_deduction_label }}: <span style="float: right; color: #dc2626;">- ₱ {{ number_format($other_deduction_amount, 2) }}</span></div>
                            @endif
                            <hr style="border:none; border-top:1px solid #cbd5e1; margin: 6px 0;" />
                            <div style="font-size: 10px; font-weight: 800;">TOTAL DEDUCTIONS: <span style="float: right;">- ₱ {{ number_format($total_deductions, 2) }}</span></div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 8px; border: none;">
                <table style="width:100%; border:none; border-collapse: collapse;">
                    <tr>
                        <td style="border:1px solid #334155; border-radius:10px; padding:10px; vertical-align: top;">
                            <div style="font-size: 10px; font-weight: 800; margin-bottom: 8px;">FINANCIAL SUMMARY</div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 4px;">GROSS WOOD AMOUNT: <span style="float: right; color: #0f172a;">₱ {{ number_format($gross_wood_amount, 2) }}</span></div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 4px;">ADD DRIVER'S ASSISTANCE: <span style="float: right; color: #15803d;">+ ₱ {{ number_format($driver_assistance, 2) }}</span></div>
                            <div style="font-size: 9.5px; color: #475569; margin-bottom: 6px;">LESS TOTAL DEDUCTIONS: <span style="float: right;">- ₱ {{ number_format($total_deductions, 2) }}</span></div>
                            <hr style="border:none; border-top:1px solid #cbd5e1; margin: 6px 0;" />
                            <div style="margin-top: 8px; font-size: 10px; font-weight: 800; color:#0f172a;">NET AMOUNT PAYABLE TO SUPPLIER</div>
                            <div style="margin-top: 4px; font-size: 18px; font-weight: 900; color:#0f172a;">₱ {{ number_format($net_payable, 2) }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SIGNATURE SECTION -->
    <table class="sig-table text-center">
        <tr>
            <td style="width: 33%;">
                <div class="sig-line"></div>
                <div class="label uppercase">PREPARED BY</div>
                <div class="val">{{ $truckLoad->scaled_by ?? 'Scaler Staff' }}</div>
            </td>
            <td style="width: 33%;">
                <div class="sig-line"></div>
                <div class="label uppercase">REVIEWED BY</div>
                <div class="val">Supplier / Driver</div>
            </td>
            <td style="width: 33%;">
                <div class="sig-line"></div>
                <div class="label uppercase">APPROVED BY</div>
                <div class="val">RMD Management</div>
            </td>
        </tr>
    </table>

</body>
</html>
