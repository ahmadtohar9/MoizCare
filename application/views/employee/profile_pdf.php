<!DOCTYPE html>
<html lang="id-ID">
<head>
    <meta charset="utf-8">
    <title>Profil Pegawai</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        h1, h2, h3, h4 { margin: 0; color: #111418; }
        .header {
            width: 100%;
            border-bottom: 2px solid #1173d4;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: table;
        }
        .header-content {
            display: table-cell;
            vertical-align: middle;
        }
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #1173d4;
            text-transform: uppercase;
        }
        .header-subtitle { font-size: 12px; color: #666; }
        
        /* Employee Identity Block */
        .identity-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .photo-cell {
            display: table-cell;
            width: 120px;
            vertical-align: top;
            text-align: center;
        }
        .photo-img {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .info-cell {
            display: table-cell;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .name-highlight { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-bottom: 5px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; color: #fff; text-transform: uppercase; margin-bottom: 15px;}
        .badge-active { background: #10b981; }
        .badge-inactive { background: #ef4444; }

        .info-table { border-collapse: collapse; width: 100%; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .label { width: 120px; font-weight: bold; color: #64748b; font-size: 11px; text-transform: uppercase;}
        .sep { width: 15px; color: #cbd5e1; }
        .val { font-weight: bold; color: #1e293b; }

        /* Section Titles */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1173d4;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        /* 50-50 Layout */
        .split-layout { display: table; width: 100%; }
        .col-left { display: table-cell; width: 48%; vertical-align: top; }
        .col-right { display: table-cell; width: 48%; vertical-align: top; padding-left: 4%; }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        .data-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        
        .footer-note {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-title">Dossier Pegawai</div>
            <div class="header-subtitle">Moiz Care HRIS - Data Personal & Akademik</div>
        </div>
        <div class="header-content" style="text-align: right;">
            <div style="font-size: 10px; color: #666;">Dicetak: <?= date('d M Y H:i') ?></div>
        </div>
    </div>

    <!-- Identity Overview -->
    <div class="identity-box">
        <div class="photo-cell">
            <?php 
                $photo_url = $employee->photo ? base_url($employee->photo) : 'https://ui-avatars.com/api/?name='.urlencode($employee->full_name).'&size=200&background=1173d4&color=fff';
                // Note: mPDF works best with local files for images if possible, but URLs usually work if allow_url_fopen is true
            ?>
            <img src="<?= $photo_url ?>" class="photo-img" />
        </div>
        <div class="info-cell">
            <div class="name-highlight"><?= $employee->full_name ?></div>
            <?php 
                $status = strtoupper($employee->status_employee);
                $badge_class = in_array($status, ['PERMANENT', 'CONTRACT', 'PROBATION']) ? 'badge-active' : 'badge-inactive';
            ?>
            <div class="badge <?= $badge_class ?>"><?= $status ?></div>
            
            <table class="info-table" style="width: 80%;">
                <tr>
                    <td class="label">NIP / ID</td><td class="sep">:</td><td class="val"><?= $employee->nip ?: '-' ?></td>
                </tr>
                <tr>
                    <td class="label">Departemen</td><td class="sep">:</td><td class="val"><?= $employee->unit_name ?: '-' ?></td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td><td class="sep">:</td><td class="val"><?= $employee->position_name ?: '-' ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Details Split -->
    <div class="split-layout">
        <!-- PRIBADI -->
        <div class="col-left">
            <div class="section-title">Informasi Pribadi</div>
            <table class="info-table">
                <tr><td class="label">NIK (KTP)</td><td class="sep">:</td><td class="val"><?= $employee->nik ?: '-' ?></td></tr>
                <tr><td class="label">TTL</td><td class="sep">:</td><td class="val"><?= $employee->birth_place ?: '-' ?>, <?= $employee->birth_date ? date('d-m-Y', strtotime($employee->birth_date)) : '-' ?></td></tr>
                <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td class="val"><?= $employee->gender == 'L' ? 'Laki-Laki' : ($employee->gender == 'P' ? 'Perempuan' : '-') ?></td></tr>
                <tr><td class="label">Agama</td><td class="sep">:</td><td class="val"><?= ucwords($employee->religion) ?: '-' ?></td></tr>
                <tr><td class="label">Gol. Darah</td><td class="sep">:</td><td class="val"><?= $employee->blood_type ?: '-' ?></td></tr>
                <tr><td class="label">Nomor HP/WA</td><td class="sep">:</td><td class="val"><?= $employee->phone ?: '-' ?></td></tr>
                <tr><td class="label" style="vertical-align: top;">Alamat Domisili</td><td class="sep" style="vertical-align: top;">:</td><td class="val"><?= $employee->current_address ?: '-' ?></td></tr>
                <tr><td class="label" style="vertical-align: top;">Alamat KTP</td><td class="sep" style="vertical-align: top;">:</td><td class="val"><?= $employee->ktp_address ?: '-' ?></td></tr>
            </table>
        </div>

        <!-- KEPEGAWAIAN -->
        <div class="col-right">
            <div class="section-title">Data Kepegawaian</div>
            <table class="info-table">
                <tr><td class="label">Tgl Bergabung</td><td class="sep">:</td><td class="val"><?= $employee->join_date ? date('d-m-Y', strtotime($employee->join_date)) : '-' ?></td></tr>
                <tr><td class="label">Sisa Cuti</td><td class="sep">:</td><td class="val"><?= $employee->remaining_leaves ?> Hari</td></tr>
                <tr><td class="label">Gaji Pokok</td><td class="sep">:</td><td class="val">Rp <?= number_format($employee->basic_salary ?: 0, 0, ',', '.') ?></td></tr>
            </table>
            
            <div class="section-title" style="margin-top: 20px;">Informasi Bank</div>
            <table class="info-table">
                <tr><td class="label">Nama Bank</td><td class="sep">:</td><td class="val"><?= $employee->bank_name ?: '-' ?></td></tr>
                <tr><td class="label">No. Rekening</td><td class="sep">:</td><td class="val"><?= $employee->bank_account ?: '-' ?></td></tr>
                <tr><td class="label">Atas Nama</td><td class="sep">:</td><td class="val"><?= $employee->bank_account_name ?: '-' ?></td></tr>
            </table>
        </div>
    </div>

    <!-- FAMILY SUMMARY -->
    <div class="section-title">Susunan Keluarga</div>
    <?php if(empty($family)): ?>
        <p style="font-style: italic; color: #64748b; font-size: 11px;">Belum ada entri data keluarga.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Lengkap</th>
                    <th style="width: 15%;">Hubungan</th>
                    <th style="width: 15%;">Jenis Kelamin</th>
                    <th style="width: 30%;">Pekerjaan / Pendidikan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($family as $f): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><strong><?= $f->name ?></strong><br><span style="font-size:9px; color:#64748b;">NIK: <?= $f->nik ?: '-' ?></span></td>
                    <td style="text-transform: capitalize;"><?= $f->relation ?></td>
                    <td><?= $f->gender == 'L' ? 'Laki-Laki' : 'Perempuan' ?></td>
                    <td><?= $f->job ?: '-' ?> / <?= $f->education ?: '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- DOCUMENT SUMMARY -->
    <div class="section-title">Arsip Berkas</div>
    <?php if(empty($documents)): ?>
        <p style="font-style: italic; color: #64748b; font-size: 11px;">Belum ada berkas terunggah.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Jenis Berkas</th>
                    <th style="width: 35%;">Keterangan</th>
                    <th style="width: 30%;">Tgl Expired</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($documents as $d): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><strong><?= $d->doc_type ?></strong></td>
                    <td><?= $d->notes ?: '-' ?></td>
                    <?php 
                        $exp = '-';
                        if($d->expiry_date) {
                            $exp = date('d-m-Y', strtotime($d->expiry_date));
                            if(strtotime($d->expiry_date) < time()) {
                                $exp .= ' (EXPIRED)';
                            }
                        }
                    ?>
                    <td><?= $exp ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer-note">
        Dokumen ini dibuat otomatis secara sah oleh Moiz Care HRIS. Data bersifat rahasia.
    </div>
</body>
</html>
