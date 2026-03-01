<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kehadiran Pegawai</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; padding: 0; margin: 0; }
        
        /* Header Style */
        .header-container { margin-bottom: 30px; border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-cell { width: 100px; text-align: left; vertical-align: middle; }
        .logo-img { max-height: 80px; width: auto; }
        .company-cell { text-align: center; vertical-align: middle; }
        .company-name { font-size: 26px; font-weight: 900; color: #1e3a8a; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 1px; }
        .company-info { font-size: 11px; color: #64748b; line-height: 1.5; font-weight: bold; }
        
        /* Title & Meta */
        .report-header { background-color: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 25px; text-align: center; }
        .report-title { font-size: 20px; font-weight: 900; text-transform: uppercase; color: #0f172a; margin: 0; letter-spacing: 2px; }
        .report-period { font-size: 11px; color: #475569; margin-top: 8px; font-weight: black; text-transform: uppercase; tracking: 0.1em; }

        /* Table Style */
        .main-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .main-table th { 
            background-color: #0f172a; 
            color: #ffffff; 
            padding: 14px 10px; 
            font-size: 10px; 
            font-weight: 900;
            text-transform: uppercase; 
            letter-spacing: 1px;
            text-align: left;
            border: none;
        }
        .main-table td { 
            padding: 12px 10px; 
            font-size: 10px; 
            border-bottom: 1px solid #f1f5f9; 
            color: #334155;
            font-weight: bold;
        }
        .main-table tr:nth-child(even) { background-color: #f8fafc; }
        
        .name-cell strong { color: #0f172a; font-size: 11px; }
        .name-cell small { color: #94a3b8; font-size: 9px; font-weight: 900; text-transform: uppercase; }

        /* Status & Accents */
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 8px; font-weight: 900; text-transform: uppercase; tracking: 0.1em; }
        .badge-present { background-color: #dcfce7; color: #15803d; }
        .badge-late { background-color: #fef3c7; color: #b45309; }
        .badge-alpha { background-color: #fee2e2; color: #b91c1c; }

        .text-late { color: #d97706; font-weight: 900; font-family: monospace; }
        .text-overtime { color: #2563eb; font-weight: 900; font-family: monospace; }

        /* Footer */
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; display: flex; justify-content: space-between; }
        .footer-left { float: left; }
        .footer-right { float: right; text-align: right; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <!-- Header Instansi -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <?php if($settings->company_logo): ?>
                        <img src="<?= FCPATH . $settings->company_logo ?>" class="logo-img">
                    <?php else: ?>
                        <div style="font-weight: 900; color: #cbd5e1; font-size: 20px;">MOIZ CARE</div>
                    <?php endif; ?>
                </td>
                <td class="company-cell">
                    <div class="company-name"><?= $settings->company_name ?></div>
                    <div class="company-info"><?= $settings->address ?></div>
                    <div class="company-info">📞 <?= $settings->contact ?> &nbsp; | &nbsp; 📧 <?= $settings->email ?></div>
                </td>
                <td width="100"></td> <!-- Spacer for center balance -->
            </tr>
        </table>
    </div>

    <!-- Report Body Header -->
    <div class="report-header">
        <h2 class="report-title">Laporan Kehadiran Pegawai</h2>
        <div class="report-period">
            Departemen: <span style="color: #1e3a8a;"><?= $department_name ?></span> &nbsp; | &nbsp; 
            Periode: <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>
        </div>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="25%">Pegawai</th>
                <th width="12%">Shift</th>
                <th width="12%">Tanggal</th>
                <th width="10%">Masuk</th>
                <th width="10%">Pulang</th>
                <th width="10%" align="center">Late</th>
                <th width="10%" align="center">Lembur</th>
                <th width="8%" align="center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            foreach($attendance as $row): 
                $late_label = '-';
                $overtime_label = '-';

                // Re-calculate durations for report
                if ($row->clock_in && $row->start_time) {
                    $clock_in_ts = strtotime($row->date . ' ' . $row->clock_in);
                    $start_ts = strtotime($row->date . ' ' . $row->start_time);
                    if ($clock_in_ts > $start_ts) {
                        $diff = $clock_in_ts - $start_ts;
                        $hours = floor($diff / 3600);
                        $mins = floor(($diff % 3600) / 60);
                        $late_label = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                    }
                }

                if ($row->clock_out && $row->end_time) {
                    $clock_out_ts = strtotime($row->date . ' ' . $row->clock_out);
                    $end_ts = strtotime($row->date . ' ' . $row->end_time);
                    if ($end_ts < strtotime($row->date . ' ' . ($row->start_time ?? '00:00:00'))) $end_ts += 86400;
                    if ($clock_out_ts > $end_ts) {
                        $diff = $clock_out_ts - $end_ts;
                        $hours = floor($diff / 3600);
                        $mins = floor(($diff % 3600) / 60);
                        $overtime_label = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                    }
                }
                
                $status_class = 'badge-alpha';
                if($row->status == 'present') $status_class = 'badge-present';
                if($row->status == 'late') $status_class = 'badge-late';
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td class="name-cell">
                    <strong><?= $row->full_name ?></strong><br>
                    <small>NIP: <?= $row->nip ?></small>
                </td>
                <td><?= $row->shift_name ?: '<span style="color:#cbd5e1">-</span>' ?></td>
                <td><?= date('d/m/Y', strtotime($row->date)) ?></td>
                <td style="font-weight: 900;"><?= $row->clock_in ? date('H:i', strtotime($row->clock_in)) : '-' ?></td>
                <td style="font-weight: 900;"><?= $row->clock_out ? date('H:i', strtotime($row->clock_out)) : '-' ?></td>
                <td align="center" class="text-late"><?= $late_label ?></td>
                <td align="center" class="text-overtime"><?= $overtime_label ?></td>
                <td align="center">
                    <span class="badge <?= $status_class ?>"><?= $row->status ?: 'Alpha' ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Signatures or Footer -->
    <div class="footer">
        <div class="footer-left">
            Dicetak oleh: <?= $this->session->userdata('full_name') ?>
        </div>
        <div class="footer-right">
            Waktu Cetak: <?= date('d F Y, H:i') ?> &nbsp; | &nbsp; Moiz Care HRIS
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
