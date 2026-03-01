<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_approval extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
    }

    public function index()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $employee = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        $this->db->select('lr.*, e.full_name, e.nip, lt.name as leave_name, d.name as unit_name');
        $this->db->from('leave_requests lr');
        $this->db->join('employees e', 'lr.employee_id = e.id');
        $this->db->join('master_units d', 'e.unit_id = d.id', 'left');
        $this->db->join('leave_types lt', 'lr.leave_type_id = lt.id');

        if ($role === 'karu') {
            // Karu ONLY sees pending requests specifically directed to them
            $this->db->where('lr.karu_id', $user_id);
            $this->db->where('lr.status', 'pending');
        } elseif ($role === 'admin') {
            // HRD/Admin ONLY sees requests already approved by Karu
            $this->db->where('lr.status', 'approved_karu');
        }

        $this->db->order_by('lr.created_at', 'DESC');
        $data['requests'] = $this->db->get()->result();

        $this->load->view('layout/header');
        $this->load->view('leave/approval_list', $data);
        $this->load->view('layout/footer');
    }

    public function process()
    {
        $id = $this->input->post('id');
        $action = $this->input->post('action'); // approve / reject
        $note = $this->input->post('note');
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        
        $req = $this->db->get_where('leave_requests', ['id' => $id])->row();

        if ($action === 'approve') {
            if ($role === 'karu') {
                $update = ['status' => 'approved_karu', 'karu_id' => $user_id, 'karu_note' => $note];
            } elseif ($role === 'admin') {
                // Final Check Quota before HRD Approve
                $quota = $this->db->get_where('leave_quotas', [
                    'employee_id' => $req->employee_id,
                    'leave_type_id' => $req->leave_type_id,
                    'year' => date('Y', strtotime($req->start_date))
                ])->row();

                if ($quota && ($quota->total_quota - $quota->used_quota) < $req->total_days) {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal Approval: Kuota cuti karyawan ini sudah habis!']);
                    return;
                }

                $update = ['status' => 'approved', 'hrd_id' => $user_id, 'hrd_note' => $note];
                
                // Update Used Quota
                if ($quota) {
                    $this->db->where('id', $quota->id)->set('used_quota', 'used_quota + ' . $req->total_days, FALSE)->update('leave_quotas');
                }
            }
        } else {
            $update = ['status' => 'rejected', ($role === 'karu' ? 'karu_note' : 'hrd_note') => $note];
        }

        $this->db->where('id', $id)->update('leave_requests', $update);
        echo json_encode(['status' => 'success', 'message' => 'Permohonan cuti berhasil diproses.']);
    }
}
