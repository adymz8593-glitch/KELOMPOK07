<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $gaji->karyawan->nama_karyawan }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { color: #4f46e5; margin: 0; text-transform: uppercase; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 8px; border-bottom: 1px solid #eee; }
        .total-row { background: #f8fafc; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 40px; text-align: right; }
        .stamp { margin-top: 50px; display: inline-block; border-top: 1px solid #000; width: 150px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SLIP GAJI KARYAWAN</h2>
        <p>Sistem E-Payroll - Periode {{ $gaji->bulan }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td width="15%">Nama</td><td>: <strong>{{ $gaji->karyawan->nama_karyawan }}</strong></td>
            <td width="15%">Tgl Cair</td><td>: {{ $gaji->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>NIK</td><td>: #{{ $gaji->karyawan->nik }}</td>
            <td>Status</td><td>: {{ $gaji->status_pembayaran ?? 'Lunas' }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr style="background: #4f46e5; color: white;">
                <th align="left" style="padding: 8px;">Keterangan</th>
                <th align="right" style="padding: 8px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Gaji Pokok</td><td align="right">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td></tr>
            <tr><td>Tunjangan</td><td align="right">Rp {{ number_format($gaji->tunjangan ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Potongan (Absensi/Alpa)</td><td align="right" style="color: red;">- Rp {{ number_format($gaji->potongan, 0, ',', '.') }}</td></tr>
            <tr class="total-row">
                <td>TOTAL TERIMA (THP)</td>
                <td align="right">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
        <div class="stamp">
            <p>Admin Payroll</p>
        </div>
    </div>
</body>
</html>