<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1f2937; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
    .header h1 { color: #1e40af; font-size: 20px; margin: 0; }
    .section { margin-bottom: 18px; }
    .section h3 { background: #f4f6fb; padding: 6px 10px; border-left: 4px solid #1e40af; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 4px 8px; }
    .label { font-weight: bold; width: 180px; }
    .signatures { margin-top: 40px; display: flex; justify-content: space-between; }
    .sign-box { width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 6px; }
</style>
</head>
<body>
    <div class="header">
        <h1>MOTORCYCLE CONTRACT AGREEMENT</h1>
        <p>Contract Reference: #{{ $contract->id }} | Date: {{ $contract->created_at->format('d M Y') }}</p>
    </div>

    <div class="section">
        <h3>Client Information</h3>
        <table>
            <tr><td class="label">Full Name</td><td>{{ $contract->user->full_name }}</td></tr>
            <tr><td class="label">Phone</td><td>{{ $contract->user->phone }}</td></tr>
            <tr><td class="label">Address</td><td>{{ $contract->user->address }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Motorcycle Details</h3>
        <table>
            <tr><td class="label">Brand / Model</td><td>{{ $contract->motorcycle->brand }} {{ $contract->motorcycle->model }}</td></tr>
            <tr><td class="label">Year</td><td>{{ $contract->motorcycle->year }}</td></tr>
            <tr><td class="label">Condition</td><td>{{ ucfirst($contract->motorcycle->condition) }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Contract Terms</h3>
        <table>
            <tr><td class="label">Start Date</td><td>{{ $contract->start_date }}</td></tr>
            <tr><td class="label">End Date</td><td>{{ $contract->end_date ?? 'N/A' }}</td></tr>
            <tr><td class="label">Total Amount</td><td>TZS {{ number_format($contract->total_amount, 2) }}</td></tr>
            <tr><td class="label">Paid Amount</td><td>TZS {{ number_format($contract->paid_amount, 2) }}</td></tr>
            <tr><td class="label">Outstanding Balance</td><td>TZS {{ number_format($contract->balance, 2) }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Witness</h3>
        @foreach($contract->witnesses as $w)
        <table>
            <tr><td class="label">Full Name</td><td>{{ $w->full_name }}</td></tr>
            <tr><td class="label">NIDA Number</td><td>{{ $w->nida_number }}</td></tr>
            <tr><td class="label">Phone</td><td>{{ $w->phone }}</td></tr>
            <tr><td class="label">Address</td><td>{{ $w->address }}</td></tr>
        </table>
        @endforeach
    </div>

    <div class="section">
        <h3>Guarantor</h3>
        @foreach($contract->guarantors as $g)
        <table>
            <tr><td class="label">Full Name</td><td>{{ $g->full_name }}</td></tr>
            <tr><td class="label">Phone</td><td>{{ $g->phone }}</td></tr>
            <tr><td class="label">Address</td><td>{{ $g->address }}</td></tr>
            <tr><td class="label">NIDA Number</td><td>{{ $g->nida_number }}</td></tr>
        </table>
        @endforeach
    </div>

    <div class="signatures">
        <div class="sign-box">Client Signature</div>
        <div class="sign-box">Manager Signature</div>
    </div>
</body>
</html>