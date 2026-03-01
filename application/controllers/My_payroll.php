<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_payroll extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index() {
        $this->load->view('layout/header');
        $this->load->view('payroll/my_payroll');
        $this->load->view('layout/footer');
    }

    public function get_my_slips_json() {
        $emp_id = $this->session->userdata('employee_id');

        $this->db->select('ps.*, pp.period_month, pp.period_year');
        $this->db->from('payroll_slips ps');
        $this->db->join('payroll_periods pp', 'ps.period_id = pp.id');
        $this->db->where('ps.employee_id', $emp_id);
        // Only show approved and paid (Don't show draft)
        $this->db->group_start();
        $this->db->where('pp.status', 'approved');
        $this->db->or_where('pp.status', 'paid');
        $this->db->group_end();
        $this->db->order_by('pp.period_year', 'DESC');
        $this->db->order_by('pp.period_month', 'DESC');
        $slips = $this->db->get()->result();

        $data = [];
        $no = 1;
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach($slips as $s) {
            $period_name = $months[$s->period_month] . ' ' . $s->period_year;
            $status_badge = '';
            // Checking actual period status via parent relation. But slip also has a status which is updated manually later, but currently slip status is generated as draft and stays draft unless period updates it. Wait, period controls the status as a whole.
            // I should just use $s->status if we update slip statuses, or I can pull pp.status
            $period_status = $this->db->get_where('payroll_periods', ['id' => $s->period_id])->row()->status;

            if($period_status == 'approved') $status_badge = '<span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider">Telah Rilis</span>';
            else if($period_status == 'paid') $status_badge = '<span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider">Telah Ditransfer</span>';

            $btn = '<a href="'.base_url('payroll/print_slip/'.$s->id).'" target="_blank" class="p-1.5 rounded-lg border border-blue-500 text-blue-600 hover:bg-blue-50 transition-all font-bold flex items-center justify-center gap-1 text-xs"><span class="material-symbols-outlined !text-[16px]">download</span> Download Slip</a>';

            $data[] = [
                $no++,
                '<span class="font-bold text-gray-900">'.$period_name.'</span>',
                '<span class="font-bold text-gray-700">'.$s->attendance_count.' hari hadir, '.$s->late_count.' hari telat</span>',
                '<span class="font-mono font-bold text-gray-900">Rp ' . number_format($s->net_salary, 0, ',', '.') . '</span>',
                $status_badge,
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }
}
