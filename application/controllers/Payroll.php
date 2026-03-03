<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Payroll_model');
        // if (!$this->session->userdata('logged_in')) {
        //    redirect('auth');
        // }
        // Exclude print_slip from global role check, we handle it inside
        if ($this->router->fetch_method() !== 'print_slip' && $this->router->fetch_method() !== 'send_email_single') {
            if (!in_array($this->session->userdata('role'), ['admin', 'hrd'])) {
                // show_error('Akses ditolak. Anda tidak memiliki izin untuk mengakses modul ini.', 403, 'Akses Ditolak');
            }
        }
    }

    public function index()
    {
        $this->load->view('layout/header');
        $this->load->view('payroll/index');
        $this->load->view('layout/footer');
    }

    public function get_periods_json()
    {
        $this->db->order_by('period_year', 'DESC');
        $this->db->order_by('period_month', 'DESC');
        $periods = $this->db->get('payroll_periods')->result();

        $data = [];
        $no = 1;
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($periods as $p) {
            $slip_count = $this->db->where('period_id', $p->id)->count_all_results('payroll_slips');
            $total_salary = $this->db->select_sum('net_salary')->where('period_id', $p->id)->get('payroll_slips')->row()->net_salary;

            $status_badge = '';
            if ($p->status == 'draft')
                $status_badge = '<span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold uppercase tracking-wider">Draft</span>';
            else if ($p->status == 'approved')
                $status_badge = '<span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider">Approved</span>';
            else if ($p->status == 'paid')
                $status_badge = '<span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider">Paid</span>';

            $btn = '<div class="flex gap-2">
                <a href="' . base_url('payroll/detail/' . $p->id) . '" class="p-1.5 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-all text-xs font-bold flex items-center gap-1"><span class="material-symbols-outlined !text-[16px]">visibility</span> Lihat Slip</a>';
            if ($p->status == 'draft') {
                $btn .= '<button onclick="deletePeriod(' . $p->id . ')" class="p-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-all font-bold flex items-center justify-center"><span class="material-symbols-outlined !text-[16px]">delete</span></button>';
            }
            $btn .= '</div>';

            $data[] = [
                $no++,
                '<span class="font-black text-gray-900">' . $months[$p->period_month] . ' ' . $p->period_year . '</span>',
                $slip_count . ' Pegawai',
                '<span class="font-mono font-bold text-gray-700">Rp ' . number_format($total_salary ?? 0, 0, ',', '.') . '</span>',
                $status_badge,
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function generate()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct script access allowed');

        $month = $this->input->post('month');
        $year = $this->input->post('year');

        // Check if exists
        $exist = $this->db->get_where('payroll_periods', ['period_month' => $month, 'period_year' => $year])->row();
        if ($exist) {
            echo json_encode(['status' => 'error', 'message' => 'Periode penggajian untuk bulan ' . $month . '/' . $year . ' sudah di-generate sebelumnya!']);
            return;
        }

        // Generate Period
        $period_id = $this->Payroll_model->generate_payroll($month, $year);

        if ($period_id) {
            echo json_encode(['status' => 'success', 'message' => 'Penggajian bulan ' . $month . '/' . $year . ' berhasil dibuat (Draft).', 'id' => $period_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal generate atau tidak ada data pegawai.']);
        }
    }

    public function detail($period_id)
    {
        $data['period'] = $this->db->get_where('payroll_periods', ['id' => $period_id])->row();
        if (!$data['period'])
            show_404();

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $data['period_name'] = $months[$data['period']->period_month] . ' ' . $data['period']->period_year;

        $this->load->view('layout/header');
        $this->load->view('payroll/detail', $data);
        $this->load->view('layout/footer');
    }

    public function get_slips_json($period_id)
    {
        $this->db->select('ps.*, pp.status as period_status, e.full_name, e.nip, e.phone, e.email, u.name as unit_name, pos.name as position_name');
        $this->db->from('payroll_slips ps');
        $this->db->join('payroll_periods pp', 'ps.period_id = pp.id');
        $this->db->join('employees e', 'ps.employee_id = e.id', 'left');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('master_positions pos', 'e.position_id = pos.id', 'left');
        $this->db->where('ps.period_id', $period_id);
        $slips = $this->db->get()->result();

        $data = [];
        $no = 1;
        foreach ($slips as $s) {
            $email = $s->email;
            $email_link = '';

            if ($email && in_array($s->period_status, ['approved', 'paid'])) {
                $email_link = '<button onclick="sendSingleEmail(' . $s->id . ')" class="p-1.5 px-3 rounded-lg border border-orange-500 text-orange-600 hover:bg-orange-50 transition-all font-bold flex items-center justify-center gap-1 text-[10px] uppercase tracking-wider" title="Kirim Slip ke Email"><span class="material-symbols-outlined !text-[14px]">mail</span> EMAIL</button>';
            } else if ($email) {
                $email_link = '<span class="p-1.5 px-3 rounded-lg border border-gray-300 text-gray-400 font-bold flex items-center justify-center gap-1 text-[10px] uppercase tracking-wider cursor-not-allowed" title="Harus disetujui HRD"><span class="material-symbols-outlined !text-[14px]">mail</span> EMAIL</span>';
            } else {
                $email_link = '<span class="p-1.5 px-3 rounded-lg border border-gray-300 text-gray-400 font-bold flex items-center justify-center gap-1 text-[10px] uppercase tracking-wider cursor-not-allowed" title="Email Pegawai Kosong"><span class="material-symbols-outlined !text-[14px]">mail</span> EMAIL</span>';
            }

            $btn = '<div class="flex items-center gap-2">
                <button onclick="viewSlipDetail(' . $s->id . ')" class="p-1.5 px-3 rounded-lg border border-purple-500 text-purple-600 hover:bg-purple-50 transition-all font-bold flex items-center justify-center gap-1 text-[10px] uppercase tracking-wider"><span class="material-symbols-outlined !text-[14px]">visibility</span> DETAIL</button>
                <a href="' . base_url('payroll/print_slip/' . $s->id) . '" target="_blank" class="p-1.5 px-3 rounded-lg border border-blue-500 text-blue-600 hover:bg-blue-50 transition-all font-bold flex items-center justify-center gap-1 text-[10px] uppercase tracking-wider"><span class="material-symbols-outlined !text-[14px]">print</span> PDF</a>
                ' . $email_link;

            if (in_array($s->period_status, ['draft', 'approved'])) {
                $btn .= '<button onclick="recalcIndividual(' . $s->id . ', ' . $s->employee_id . ')" class="p-1.5 px-3 rounded-lg border border-yellow-500 text-yellow-600 hover:bg-yellow-50 transition-all font-bold flex items-center justify-center gap-1 text-[10px] uppercase tracking-wider tooltip" title="Hitung Ulang Gaji"><span class="material-symbols-outlined !text-[14px]">refresh</span></button>';
            }
            $btn .= '</div>';

            $data[] = [
                $no++,
                '<div class="flex flex-col"><span class="font-bold text-gray-900">' . $s->full_name . '</span><span class="text-[10px] text-gray-500 font-bold uppercase">' . $s->nip . ' • ' . $s->unit_name . '</span></div>',
                '<span class="text-xs text-gray-600">' . ($s->position_name ?: '-') . '</span>',
                '<span class="font-bold text-gray-700">' . $s->attendance_count . ' hari</span>',
                '<span class="font-mono font-bold text-gray-900">Rp ' . number_format($s->net_salary, 0, ',', '.') . '</span>',
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get_slip_detail($slip_id)
    {
        $slip = $this->db->select('ps.*, pp.period_month, pp.period_year, e.nip, e.full_name, e.bank_name, e.bank_account, u.name as unit_name, pos.name as position_name')
            ->from('payroll_slips ps')
            ->join('payroll_periods pp', 'ps.period_id = pp.id')
            ->join('employees e', 'ps.employee_id = e.id', 'left')
            ->join('master_units u', 'e.unit_id = u.id', 'left')
            ->join('master_positions pos', 'e.position_id = pos.id', 'left')
            ->where('ps.id', $slip_id)
            ->get()->row();

        if (!$slip) {
            echo json_encode(['status' => 'error', 'message' => 'Slip tidak ditemukan']);
            return;
        }

        $details = $this->db->get_where('payroll_slip_details', ['slip_id' => $slip_id])->result();

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $slip->period_name = $months[$slip->period_month] . ' ' . $slip->period_year;

        echo json_encode(['status' => 'success', 'slip' => $slip, 'details' => $details]);
    }

    // Endpoint for Auto Broadcast WA / Email Payload
    public function get_slips_raw($period_id)
    {
        $this->db->select('payroll_slips.*, pp.status as period_status, employees.full_name, employees.phone, employees.email');
        $this->db->from('payroll_slips');
        $this->db->join('payroll_periods pp', 'payroll_slips.period_id = pp.id');
        $this->db->join('employees', 'employees.id = payroll_slips.employee_id', 'left');
        $this->db->where('payroll_slips.period_id', $period_id);
        $slips = $this->db->get()->result();

        $data = [];
        foreach ($slips as $s) {
            $phone = $s->phone;
            if (substr($phone, 0, 1) == '0') {
                $phone = '62' . substr($phone, 1);
            }

            if (in_array($s->period_status, ['approved', 'paid'])) {
                $data[] = [
                    'id' => $s->id,
                    'full_name' => $s->full_name,
                    'phone' => $phone,
                    'email' => $s->email,
                    'slip_link' => base_url('payroll/print_slip/' . $s->id)
                ];
            }
        }
        echo json_encode(['data' => $data]);
    }

    public function send_email_single()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct script access allowed');

        // Suppress CI warnings to prevent them from corrupting the JSON response
        error_reporting(0);
        ini_set('display_errors', 0);
        ob_start();

        $slip_id = $this->input->post('slip_id');

        $s = $this->db->select('ps.*, e.nip, e.full_name, e.email, e.bank_name, e.bank_account, e.bank_account_name, u.name as unit_name, pos.name as position_name')
            ->from('payroll_slips ps')
            ->join('employees e', 'ps.employee_id = e.id', 'left')
            ->join('master_units u', 'e.unit_id = u.id', 'left')
            ->join('master_positions pos', 'e.position_id = pos.id', 'left')
            ->where('ps.id', $slip_id)
            ->get()->row();

        if (!$s || empty($s->email)) {
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Email tidak tersedia']);
            return;
        }

        $settings = $this->db->get('settings')->row();
        $period = $this->db->get_where('payroll_periods', ['id' => $s->period_id])->row();

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $period_name = $months[$period->period_month] . ' ' . $period->period_year;

        // Generate temporary PDF
        $data = ['slip' => $s, 'period_name' => $period_name, 'settings' => $settings];
        $data['details'] = $this->db->get_where('payroll_slip_details', ['slip_id' => $s->id])->result();
        $data['company_logo'] = base_url($settings->company_logo ?? 'assets/images/logo.png');

        $qr_text = "Disetujui Oleh: " . (!empty($settings->hr_signature_name) ? $settings->hr_signature_name : 'Moizcare HRD') . "\n";
        $qrInfo = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($qr_text . "Tanggal: " . date('d F Y H:i:s') . "\nDigenerate oleh HRIS Moiz Care System")->size(150)->margin(0)->build();
        $data['qr_code'] = $qrInfo->getDataUri();

        $html = $this->load->view('payroll/slip_pdf', $data, TRUE);
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
            'tempDir' => FCPATH . 'uploads/temp'
        ]);
        $mpdf->WriteHTML($html);

        // Define temp path securely inside project
        $temp_dir = FCPATH . 'uploads/temp';
        if (!is_dir($temp_dir))
            mkdir($temp_dir, 0777, true);
        $pdf_filename = 'Slip_Gaji_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $s->full_name) . '_' . date('Ym') . '.pdf';
        $pdf_path = $temp_dir . '/' . $pdf_filename;
        $mpdf->Output($pdf_path, \Mpdf\Output\Destination::FILE);

        // Send Email using dynamic SMTP config
        $this->load->library('email');

        $smtp = $this->db->get('email_settings')->row();

        // Bersihkan host dari prefix ssl:// atau tls:// karena library Email CI akan menambahkannya otomatis
        $smtp_host_clean = $smtp ? str_replace(['ssl://', 'tls://'], '', $smtp->smtp_host) : 'smtp.gmail.com';

        $config = array(
            'protocol' => $smtp ? $smtp->protocol : 'smtp',
            'smtp_host' => $smtp_host_clean,
            'smtp_port' => $smtp ? $smtp->smtp_port : 465,
            'smtp_user' => $smtp ? $smtp->smtp_user : '',
            'smtp_pass' => str_replace(' ', '', $smtp ? $smtp->smtp_pass : ''), // Bersihkan spasi jika ada
            'smtp_crypto' => $smtp ? $smtp->smtp_crypto : 'ssl',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'newline' => "\r\n",
            'crlf' => "\r\n"
        );
        $this->email->initialize($config);

        $this->email->clear(TRUE);
        $sender_email = ($smtp && !empty($smtp->smtp_user)) ? $smtp->smtp_user : 'sistem@moizcare.com';
        $sender_name = !empty($settings->company_name) ? $settings->company_name : 'HRIS Moiz Care System';

        $this->email->from($sender_email, $sender_name);
        $this->email->to($s->email);
        $this->email->subject('Slip Gaji - ' . $period_name);

        $message = '<h3>Halo ' . $s->full_name . ',</h3>';
        $message .= '<p>Berikut kami lampirkan dokumen Slip Gaji Anda untuk periode <b>' . $period_name . '</b>.</p>';
        $message .= '<p>Pesan ini dibuat otomatis oleh sistem HRIS. Mohon tidak membalas email ini.</p>';
        $message .= '<p>Terima kasih,<br><b>' . (!empty($settings->company_name) ? $settings->company_name : 'Tim HRD') . '</b></p>';

        $this->email->message($message);
        $this->email->attach($pdf_path);

        if ($this->email->send()) {
            @unlink($pdf_path);
            ob_end_clean();
            echo json_encode(['status' => 'success']);
        } else {
            @unlink($pdf_path);
            $debug = $this->email->print_debugger();
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email. Pastikan setting SMTP Gmail benar.', 'debug' => $debug]);
        }
    }

    public function recalculate_individual()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct script access allowed');

        $slip_id = $this->input->post('slip_id');
        $employee_id = $this->input->post('employee_id');
        $period_id = $this->input->post('period_id');

        $this->db->trans_start();

        // Cek status periode
        $period = $this->db->get_where('payroll_periods', ['id' => $period_id])->row();
        if (!$period || in_array($period->status, ['paid'])) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal. Gaji sudah ditransfer/dibayar, tidak bisa dihitung ulang. Batal Finalkan ke DRAFT / APPROVED terlebih dahulu jika ingin memaksa.']);
            return;
        }

        // Hapus detail slip & slip lama
        $this->db->where('slip_id', $slip_id)->delete('payroll_slip_details');
        $this->db->where('id', $slip_id)->delete('payroll_slips');

        // Panggil helper internal untuk mencetak ulang slip 1 karyawan tersebut saja
        $new_id = $this->Payroll_model->recalculate_single($period_id, $employee_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghitung ulang.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Slip gaji berhasil di-kalkulasi ulang.']);
        }
    }
    public function change_status()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct script access allowed');
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $this->db->where('id', $id)->update('payroll_periods', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        echo json_encode(['status' => 'success', 'message' => 'Status diubah jadi ' . strtoupper($status)]);
    }

    public function delete_period($id)
    {
        if (!$this->input->is_ajax_request())
            exit('No direct script access allowed');

        $period = $this->db->get_where('payroll_periods', ['id' => $id])->row();
        if ($period && $period->status == 'draft') {
            // Delete slips and details
            $slips = $this->db->get_where('payroll_slips', ['period_id' => $id])->result();
            foreach ($slips as $s) {
                $this->db->delete('payroll_slip_details', ['slip_id' => $s->id]);
            }
            $this->db->delete('payroll_slips', ['period_id' => $id]);
            $this->db->delete('payroll_periods', ['id' => $id]);

            echo json_encode(['status' => 'success', 'message' => 'Periode penggajian berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hanya draft yang bisa dihapus.']);
        }
    }

    public function print_slip($slip_id)
    {
        $this->db->select('ps.*, e.full_name, e.nip, e.basic_salary as employee_basic, e.bank_name, e.bank_account, e.bank_account_name, u.name as unit_name, pos.name as position_name, pp.period_month, pp.period_year');
        $this->db->from('payroll_slips ps');
        $this->db->join('employees e', 'ps.employee_id = e.id');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('master_positions pos', 'e.position_id = pos.id', 'left');
        $this->db->join('payroll_periods pp', 'ps.period_id = pp.id');
        $this->db->where('ps.id', $slip_id);
        $data['slip'] = $this->db->get()->row();

        if (!$data['slip'])
            show_404();

        // Security check: Only allow admin/hrd or the employee themselves
        if (!in_array($this->session->userdata('role'), ['admin', 'hrd']) && $data['slip']->employee_id != $this->session->userdata('employee_id')) {
            show_error('Akses ditolak. Bukan slip gaji Anda.', 403);
        }

        // Only allow printing if status is approved or paid
        if (!in_array($this->session->userdata('role'), ['admin', 'hrd']) && $data['slip']->status == 'draft') {
            show_error('Slip belum dirilis secara final oleh HRD.', 403);
        }

        $data['details'] = $this->db->get_where('payroll_slip_details', ['slip_id' => $slip_id])->result();
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $data['period_name'] = $months[$data['slip']->period_month] . ' ' . $data['slip']->period_year;
        $settings = $this->db->get('settings')->row();
        $data['settings'] = $settings;
        $data['company_logo'] = base_url($settings->company_logo ?? 'assets/images/logo.png'); // Placeholder

        // Generate QR Code
        $qr_text = "Disetujui Oleh: " . (!empty($settings->hr_signature_name) ? $settings->hr_signature_name : 'Moizcare HRD') . "\n";
        $qr_text .= "Tanggal: " . date('d F Y H:i:s') . "\n";
        $qr_text .= "Digenerate oleh HRIS Moiz Care System";

        $qrInfo = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->writerOptions([])
            ->data($qr_text)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
            ->size(150)
            ->margin(0)
            ->build();

        $data['qr_code'] = $qrInfo->getDataUri();

        $html = $this->load->view('payroll/slip_pdf', $data, TRUE);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4', // A4 Portrait
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
            'tempDir' => FCPATH . 'uploads/temp'
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('Slip_Gaji_' . $data['slip']->nip . '_' . date('Ym') . '.pdf', 'I');
    }

    public function export_sender($period_id)
    {
        // 1. Get Settings & Setup Paths
        $settings = $this->db->get('settings')->row();

        // 2. Load Period Info
        $period = $this->db->get_where('payroll_periods', ['id' => $period_id])->row();
        if (!$period)
            show_error('Periode tidak ditemukan.', 404);

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $period_name = $months[$period->month] . ' ' . $period->year;

        // Setup Export Directories
        $export_dir = FCPATH . 'assets/export/wa_sender/period_' . $period_id . '/';
        $pdf_dir = $export_dir . 'PDF_Slips/';

        if (!is_dir($export_dir))
            mkdir($export_dir, 0777, true);
        if (!is_dir($pdf_dir))
            mkdir($pdf_dir, 0777, true);

        // 3. Load Slips Data
        $this->db->select('ps.*, pp.status as period_status, e.full_name, e.phone');
        $this->db->from('payroll_slips ps');
        $this->db->join('payroll_periods pp', 'ps.period_id = pp.id');
        $this->db->join('employees e', 'e.id = ps.employee_id', 'left');
        $this->db->where('ps.period_id', $period_id);
        $this->db->where_in('pp.status', ['approved', 'paid']); // Only include valid period statuses
        $slips = $this->db->get()->result();

        if (empty($slips)) {
            echo "<script>alert('Belum ada slip gaji yang disetujui (Approved) untuk diexport.'); window.close();</script>";
            return;
        }

        // 4. Require PhpSpreadsheet
        require_once FCPATH . 'vendor/autoload.php';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Excel Headers for WA Sender
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Nomor WA');
        $sheet->setCellValue('C1', 'Pesan');
        $sheet->setCellValue('D1', 'Attachment PDF');

        $row_num = 2;
        $file_list = [];

        // 5. Generate PDFs and fill Excel
        foreach ($slips as $s) {
            // Process Phone Number
            $phone = $s->phone;
            if (substr($phone, 0, 1) == '0') {
                $phone = '62' . substr($phone, 1);
            }

            if (empty($phone))
                continue; // Skip if no phone

            // 5a. Generate PDF physically via mPDF
            $pdf_filename = 'Slip_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $s->full_name) . '_' . date('Ym') . '.pdf';
            $pdf_path = $pdf_dir . $pdf_filename;

            // Build PDF Data
            $data = ['period_name' => $period_name, 'settings' => $settings];
            $full_slip = $this->db->select('ps.*, e.nip, e.full_name, e.bank_name, e.bank_account, e.bank_account_name, e.attendance_count, e.late_count, e.absent_count, u.name as unit_name, pos.name as position_name')->from('payroll_slips ps')->join('employees e', 'ps.employee_id = e.id', 'left')->join('master_units u', 'e.unit_id = u.id', 'left')->join('master_positions pos', 'e.position_id = pos.id', 'left')->where('ps.id', $s->id)->get()->row();
            $data['slip'] = $full_slip;

            $data['details'] = $this->db->get_where('payroll_slip_details', ['slip_id' => $s->id])->result();
            $data['company_logo'] = base_url($settings->company_logo ?? 'assets/images/logo.png');

            // QR
            $qr_text = "Disetujui Oleh: " . (!empty($settings->hr_signature_name) ? $settings->hr_signature_name : 'Moizcare HRD') . "\n";
            $qrInfo = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($qr_text . "Tanggal: " . date('d F Y H:i:s') . "\nDigenerate oleh HRIS Moiz Care System")->size(150)->margin(0)->build();
            $data['qr_code'] = $qrInfo->getDataUri();

            $html = $this->load->view('payroll/slip_pdf', $data, TRUE);
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_right' => 15,
                'tempDir' => FCPATH . 'uploads/temp'
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdf_path, \Mpdf\Output\Destination::FILE);

            $file_list[] = $pdf_path; // Track for zip

            // 5b. Write to Excel Row
            $wa_text = "Halo *" . $s->full_name . "*,\nBerikut SLIP GAJI Anda periode *" . $period_name . "*.\nFile terlampir.\nHRD Moiz Care";

            $sheet->setCellValue('A' . $row_num, $s->full_name);
            $sheet->setCellValueExplicit('B' . $row_num, $phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row_num, $wa_text);
            $sheet->setCellValue('D' . $row_num, 'PDF_Slips/' . $pdf_filename);

            $row_num++;
        }

        if (count($file_list) == 0) {
            echo "<script>alert('Tidak ada slip yang diproses (Mungkin tidak ada nomor WA).'); window.close();</script>";
            return;
        }

        // 6. Save Excel
        $excel_path = $export_dir . 'DaftarKontak.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($excel_path);

        // 7. Zip Everything
        $zip_filename = 'WA_Sender_Export_' . date('Ym_His') . '.zip';
        $zip_path = $export_dir . $zip_filename;

        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($excel_path, 'DaftarKontak.xlsx');
            $zip->addEmptyDir('PDF_Slips');
            foreach ($file_list as $file) {
                // Folder internal zip
                $zip->addFile($file, 'PDF_Slips/' . basename($file));
            }
            $zip->close();
        }

        // 8. Download Zip
        if (file_exists($zip_path)) {
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zip_filename);
            header('Content-Length: ' . filesize($zip_path));
            readfile($zip_path);
        }
    }
}
