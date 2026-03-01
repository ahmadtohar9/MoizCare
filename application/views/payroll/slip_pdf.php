<!DOCTYPE html>
<html lang="id-ID">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - <?= $slip->full_name ?></title>
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10.5pt; color: #1e293b; line-height: 1.5; }
        .container { width: 100%; box-sizing: border-box; }
        
        /* Header */
        .header { width: 100%; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .logo { max-width: 60px; max-height: 60px; object-fit: contain; }
        .company-name { font-size: 16pt; font-weight: bold; color: #1e3a8a; margin: 0; padding: 0; text-transform: uppercase; }
        .company-info { font-size: 8.5pt; color: #64748b; line-height: 1.4; }
        .title { font-size: 16pt; font-weight: bold; color: #0f172a; text-align: right; letter-spacing: 1px; }
        .period { font-size: 10pt; color: #64748b; text-align: right; margin-top: 3px; }
        
        /* Employee Info */
        .info-table { width: 100%; margin-bottom: 25px; background-color: #f8fafc; border-radius: 6px; padding: 12px; border: 1px solid #e2e8f0; }
        .info-table td { padding: 4px 6px; font-size: 9.5pt; }
        .info-label { font-weight: bold; color: #475569; width: 110px; }
        
        /* Layout Tables */
        .calc-wrapper { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .calc-wrapper td { vertical-align: top; padding: 0; }
        .calc-left { padding-right: 15px; }
        .calc-middle { width: 1px; border-right: 1px dashed #cbd5e1; }
        .calc-right { padding-left: 15px; }
        
        .section-title { font-size: 10pt; font-weight: bold; color: #0f172a; border-bottom: 2px solid #0f172a; padding-bottom: 5px; margin-bottom: 12px; text-transform: uppercase; }
        
        .item-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
        .item-table td { padding: 6px 0; border-bottom: 1px solid #f1f5f9; }
        .item-amt { text-align: right; font-family: 'Courier New', Courier, monospace; font-weight: bold; }
        
        .subtotal { font-weight: bold; color: #0f172a; }
        .subtotal td { border-top: 1px dashed #94a3b8; border-bottom: none; padding-top: 8px; margin-top: 4px; }
        
        /* Net Pay */
        .net-pay-box { width: 100%; background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 6px solid #2563eb; padding: 15px 20px; margin-bottom: 20px; border-radius: 4px; display: table; }
        .net-pay-table { width: 100%; }
        .net-label { font-size: 12pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .net-amount { font-size: 18pt; font-weight: bold; color: #1e40af; text-align: right; font-family: 'Courier New', Courier, monospace; }
        
        /* Signature */
        .footer-table { width: 100%; text-align: center; margin-top: 40px; font-size: 9.5pt; }
        .footer-table td { width: 50%; vertical-align: bottom; }
        .qr-code { width: 80px; height: 80px; margin: 8px auto; display: block; }
        .sign-name { font-weight: bold; color: #0f172a; text-decoration: underline; font-size: 11pt; margin-bottom: 4px; }
        .sign-title { font-size: 9pt; color: #64748b; text-transform: uppercase; }
        
        .payment-info { font-size: 9.5pt; background-color: #f8fafc; padding: 12px; border: 1px solid #e2e8f0; border-radius: 4px; color: #475569; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header">
            <tr>
                <?php if(!empty($settings->company_logo)): ?>
                <td style="width: 70px;">
                    <img src="<?= $company_logo ?>" class="logo" alt="Logo">
                </td>
                <?php endif; ?>
                <td style="vertical-align: middle;">
                    <div class="company-name"><?= !empty($settings->company_name) ? strtoupper($settings->company_name) : 'COMPANY NAME' ?></div>
                    <div class="company-info">
                        <?= !empty($settings->address) ? nl2br($settings->address) : 'Alamat instansi tidak diatur' ?><br>
                        Telp: <?= !empty($settings->contact) ? $settings->contact : '-' ?> | Email: <?= !empty($settings->email) ? $settings->email : '-' ?>
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div class="title">SLIP GAJI</div>
                    <div class="period">Periode: <strong><?= $period_name ?></strong></div>
                </td>
            </tr>
        </table>
        
        <!-- Employee Info -->
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <table style="width: 100%;">
                        <tr><td class="info-label">NIP / ID</td><td>: <?= $slip->nip ?></td></tr>
                        <tr><td class="info-label">Nama Pegawai</td><td>: <span style="font-weight:bold; color:#0f172a; font-size: 10.5pt;"><?= strtoupper($slip->full_name) ?></span></td></tr>
                        <tr><td class="info-label">Departemen</td><td>: <?= $slip->unit_name ?: '-' ?></td></tr>
                        <tr><td class="info-label">Jabatan</td><td>: <?= $slip->position_name ?: '-' ?></td></tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;">
                     <table style="width: 100%;">
                        <tr><td class="info-label">Status Generate</td><td>: 
                            <span style="background-color: <?= ($slip->status == 'approved' || $slip->status == 'paid') ? '#10b981' : '#f59e0b' ?>; color: white; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8pt;">
                                <?= strtoupper($slip->status) ?>
                            </span>
                        </td></tr>
                        <tr><td class="info-label">Total Hadir</td><td>: <?= $slip->attendance_count ?> Hari</td></tr>
                        <tr><td class="info-label">Keterlambatan</td><td>: <?= $slip->late_count ?> Kali</td></tr>
                        <tr><td class="info-label">Absen / Alpha</td><td>: <?= $slip->absent_count ?> Hari</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Calculations -->
        <table class="calc-wrapper">
            <tr>
                <td class="calc-left" style="width: 49%;">
                    <div class="section-title">PENERIMAAN (+)</div>
                    <table class="item-table">
                        <tr>
                            <td>Gaji Pokok (Basic)</td>
                            <td class="item-amt">Rp <?= number_format($slip->basic_salary, 0, ',', '.') ?></td>
                        </tr>
                        <?php 
                        $has_allowance = false;
                        foreach($details as $d): 
                            if($d->type == 'allowance'): 
                                $has_allowance = true;
                        ?>
                        <tr>
                            <td><?= $d->description ?></td>
                            <td class="item-amt">Rp <?= number_format($d->amount, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; endforeach; ?>
                    </table>
                </td>
                
                <td class="calc-middle"></td>

                <td class="calc-right" style="width: 49%;">
                    <div class="section-title">POTONGAN (-)</div>
                    <table class="item-table">
                        <?php 
                        $has_deduction = false;
                        foreach($details as $d): 
                            if($d->type == 'deduction'): 
                                $has_deduction = true;
                        ?>
                        <tr>
                            <td><?= $d->description ?></td>
                            <td class="item-amt" style="color: #e11d48;">(Rp <?= number_format($d->amount, 0, ',', '.') ?>)</td>
                        </tr>
                        <?php endif; endforeach; ?>
                        
                        <?php if(!$has_deduction): ?>
                        <tr><td colspan="2" style="color: #94a3b8; font-style: italic; text-align: center; padding: 10px;">Tidak ada potongan bulan ini.</td></tr>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="calc-left" style="padding-top: 15px; width: 49%;">
                    <table class="item-table">
                        <tr class="subtotal">
                            <td>TOTAL PENERIMAAN</td>
                            <td class="item-amt" style="color: #059669;">Rp <?= number_format($slip->basic_salary + $slip->total_allowance, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </td>

                <td class="calc-middle"></td>

                <td class="calc-right" style="padding-top: 15px; width: 49%;">
                    <table class="item-table">
                        <tr class="subtotal">
                            <td>TOTAL POTONGAN</td>
                            <td class="item-amt" style="color: #e11d48;">(Rp <?= number_format($slip->total_deduction, 0, ',', '.') ?>)</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Net Pay -->
        <div class="net-pay-box">
            <table class="net-pay-table">
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="net-label">TOTAL GAJI DIBAYARKAN</div>
                        <div style="font-size: 9pt; color: #475569; margin-top: 4px;">Pendapatan Bersih <i>(Net Salary)</i></div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="net-amount">Rp <?= number_format($slip->net_salary, 0, ',', '.') ?></div>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php if(!empty($slip->bank_account)): ?>
        <div class="payment-info">
            <b>Informasi Transfer:</b> Pembayaran dilakukan ke rekening <b><?= $slip->bank_name ?></b> No. <b><?= $slip->bank_account ?></b> atas nama <b><?= strtoupper($slip->bank_account_name) ?></b>.
        </div>
        <?php else: ?>
        <div class="payment-info">
            <b>Informasi Pembayaran:</b> Pembayaran dilakukan secara Tunai / Cash (Data Rekening tidak tersedia).
        </div>
        <?php endif; ?>

        <!-- Footer / Signature -->
        <table class="footer-table" cellpadding="0" cellspacing="0" style="margin-top: 50px;">
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: center;">
                    <!-- Empmty space for city/date equivalent -->
                    <div style="font-size: 9.5pt; color: white; display: block; margin-bottom: 5px;">Surabaya, XXXXXXXX</div>
                    <div style="font-size: 9.5pt; display: block;">Diterima Oleh,</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: center;">
                    <div style="font-size: 9.5pt; display: block; margin-bottom: 5px;">Surabaya, <?= date('d F Y', strtotime($period_name ? "1 $period_name" : date('Y-m-d'))) ?></div>
                    <div style="font-size: 9.5pt; display: block;">Disetujui Oleh,</div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; vertical-align: middle; text-align: center; height: 100px;">
                    <!-- Spasi untuk tanda tangan manual -->
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: center; height: 100px;">
                    <?php if(!empty($qr_code)): ?>
                        <img src="<?= $qr_code ?>" class="qr-code" alt="QR Code Signature" style="margin: 0 auto; display: block;">
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; vertical-align: bottom; text-align: center;">
                    <div class="sign-name"><?= strtoupper($slip->full_name) ?></div>
                    <div class="sign-title">Pihak Pegawai</div>
                </td>
                <td style="width: 50%; vertical-align: bottom; text-align: center;">
                    <div class="sign-name"><?= strtoupper(!empty($settings->hr_signature_name) ? $settings->hr_signature_name : 'NAMA HRD') ?></div>
                    <div class="sign-title"><?= !empty($settings->hr_signature_title) ? strtoupper($settings->hr_signature_title) : 'HR MANAGER' ?></div>
                </td>
            </tr>
        </table>
        
        <div style="text-align: center; margin-top: 40px; font-size: 8pt; color: #94a3b8; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
            Dokumen ini digenerate sah secara otomatis oleh <b>HRIS Moiz Care System</b>.<br>
            Memiliki keamanan QR Code sebagai validasi persetujuan digital yang sah.
        </div>
    </div>
</body>
</html>
