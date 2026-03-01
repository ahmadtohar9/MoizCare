<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_report extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        
        $role = $this->session->userdata('role');
        if (!in_array($role, ['admin', 'karu'])) redirect('dashboard');
    }

    public function index()
    {
        $data['departments'] = $this->db->get('master_units')->result();
        $data['leave_types'] = $this->db->get('leave_types')->result();

        $this->load->view('layout/header');
        $this->load->view('leave/report_history', $data);
        $this->load->view('layout/footer');
    }

    public function history_json()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $dept = $this->input->get('unit_id');
        $status = $this->input->get('status');

        $this->db->select('lr.*, e.full_name, e.nip, lt.name as leave_name, u.name as unit_name, k.full_name as karu_name');
        $this->db->from('leave_requests lr');
        $this->db->join('employees e', 'lr.employee_id = e.id');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('leave_types lt', 'lr.leave_type_id = lt.id');
        $this->db->join('employees k', 'lr.karu_id = k.user_id', 'left');

        if ($role === 'karu') {
            $this->db->where('e.unit_id', $emp->unit_id);
        } elseif ($dept) {
            $this->db->where('e.unit_id', $dept);
        }

        if ($start) $this->db->where('lr.start_date >=', $start);
        if ($end) $this->db->where('lr.end_date <=', $end);
        if ($status) $this->db->where('lr.status', $status);

        $this->db->order_by('lr.created_at', 'DESC');
        $rows = $this->db->get()->result();

        $data = [];
        $no = 1;
        foreach($rows as $r) {
            $status_badge = '';
            if($r->status == 'pending') $status_badge = '<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-600 text-[9px] font-black uppercase">Pending</span>';
            if($r->status == 'approved_karu') $status_badge = '<span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[9px] font-black uppercase">Acc Karu</span>';
            if($r->status == 'approved') $status_badge = '<span class="px-2 py-0.5 rounded bg-green-50 text-green-600 text-[9px] font-black uppercase">Disetujui HRD</span>';
            if($r->status == 'rejected') $status_badge = '<span class="px-2 py-0.5 rounded bg-red-50 text-red-600 text-[9px] font-black uppercase">Ditolak</span>';

            $data[] = [
                $no++,
                "<b>$r->full_name</b><br><small class='text-gray-400'>$r->nip | $r->unit_name</small>",
                "$r->leave_name",
                date('d/m/y', strtotime($r->start_date)) . " - " . date('d/m/y', strtotime($r->end_date)),
                "<b>$r->total_days</b> Hari",
                $status_badge,
                "<small class='italic text-gray-500'>".($r->karu_note ?: '-')."</small>"
            ];
        }

        echo json_encode(['data' => $data]);
    }

    public function balances()
    {
        $data['departments'] = $this->db->get('master_units')->result();
        $this->load->view('layout/header');
        $this->load->view('leave/report_balances', $data);
        $this->load->view('layout/footer');
    }

    public function balances_json()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();
        $dept = $this->input->get('unit_id');

        $this->db->select('e.id, e.full_name, e.nip, u.name as unit_name');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');

        if ($role === 'karu') {
            $this->db->where('e.unit_id', $emp->unit_id);
        } elseif ($dept) {
            $this->db->where('e.unit_id', $dept);
        }

        $employees = $this->db->get()->result();
        $leave_types = $this->db->get('leave_types')->result();

        $data = [];
        $no = 1;
        foreach($employees as $e) {
            $quotas_html = '<div class="flex flex-wrap gap-2">';
            foreach($leave_types as $lt) {
                $q = $this->db->get_where('leave_quotas', [
                    'employee_id' => $e->id, 
                    'leave_type_id' => $lt->id,
                    'year' => date('Y')
                ])->row();
                
                if($q) {
                    $used = $q->used_quota;
                    $rem = $q->total_quota - $used;
                    $quotas_html .= "<div class='bg-gray-50 p-2 rounded-xl border border-gray-100 min-w-[80px]'>
                                        <p class='text-[8px] font-black text-gray-400 uppercase'>$lt->name</p>
                                        <p class='text-xs font-black text-gray-800'>$rem <span class='text-[8px] text-gray-400'>SISA</span></p>
                                        <p class='text-[8px] font-bold text-blue-500'>Pake: $used</p>
                                     </div>";
                }
            }
            $quotas_html .= '</div>';

            $data[] = [
                $no++,
                "<b>$e->full_name</b><br><small class='text-gray-400'>$e->nip | $e->unit_name</small>",
                $quotas_html
            ];
        }

        echo json_encode(['data' => $data]);
    }
}
