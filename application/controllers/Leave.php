<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    // Portal Cuti Pegawai
    public function my_leave()
    {
        $user_id = $this->session->userdata('user_id');
        $employee = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        if (!$employee) {
             show_error('Profil pegawai tidak ditemukan.');
        }

        $data['employee'] = $employee;
        $data['leave_types'] = $this->db->get('leave_types')->result();
        
        // Get list of Karu (approvers) - ONLY from the same unit
        $data['approvers'] = $this->db->select('u.id as user_id, e.full_name, m.name as unit_name')
                                     ->from('users u')
                                     ->join('employees e', 'e.user_id = u.id')
                                     ->join('master_units m', 'e.unit_id = m.id', 'left')
                                     ->where('u.role', 'karu')
                                     ->where('e.unit_id', $employee->unit_id)
                                     ->get()->result();
        
        // Quotas
        $data['quotas'] = $this->db->select('lq.*, lt.name as leave_name')
                                 ->from('leave_quotas lq')
                                 ->join('leave_types lt', 'lq.leave_type_id = lt.id')
                                 ->where('lq.employee_id', $employee->id)
                                 ->where('lq.year', date('Y'))
                                 ->get()->result();

        // Requests
        $data['requests'] = $this->db->select('lr.*, lt.name as leave_name')
                                    ->from('leave_requests lr')
                                    ->join('leave_types lt', 'lr.leave_type_id = lt.id')
                                    ->where('lr.employee_id', $employee->id)
                                    ->order_by('lr.created_at', 'DESC')
                                    ->get()->result();

        $this->load->view('layout/header');
        $this->load->view('leave/my_leave', $data);
        $this->load->view('layout/footer');
    }

    public function submit_request()
    {
        $user_id = $this->session->userdata('user_id');
        $employee = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        $start = $this->input->post('start_date');
        $end   = $this->input->post('end_date');
        $type_id = $this->input->post('leave_type_id');
        
        // Calculate days
        $d_start = new DateTime($start);
        $d_end = new DateTime($end);
        $diff = $d_start->diff($d_end);
        $total_days = $diff->days + 1;

        // Check Quota
        $quota = $this->db->get_where('leave_quotas', [
            'employee_id' => $employee->id,
            'leave_type_id' => $type_id,
            'year' => date('Y')
        ])->row();

        if ($quota && ($quota->total_quota - $quota->used_quota) < $total_days) {
            $this->session->set_flashdata('error', 'Sisa kuota cuti anda tidak mencukupi!');
            redirect('leave/my_leave');
        }

        $data = [
            'employee_id' => $employee->id,
            'leave_type_id' => $type_id,
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $total_days,
            'reason' => $this->input->post('reason'),
            'karu_id' => $this->input->post('karu_id'), // Selected Karu
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Handle Attachment
        if (!empty($_FILES['attachment']['name'])) {
            $config['upload_path']   = './uploads/leave/';
            $config['allowed_types'] = 'pdf|jpg|png|jpeg';
            $config['file_name']     = 'leave_' . time();
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('attachment')) {
                $data['attachment'] = 'uploads/leave/' . $this->upload->data('file_name');
            }
        }

        $this->db->insert('leave_requests', $data);
        $this->session->set_flashdata('success', 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan Karu.');
        redirect('leave/my_leave');
    }
    public function delete_request($id)
    {
        $user_id = $this->session->userdata('user_id');
        $employee = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        // Security Check: Existence, Ownership, and Status
        $request = $this->db->get_where('leave_requests', [
            'id' => $id,
            'employee_id' => $employee->id,
            'status' => 'pending'
        ])->row();

        if ($request) {
            // Delete attachment if it exists
            if (!empty($request->attachment) && file_exists($request->attachment)) {
                unlink($request->attachment);
            }

            $this->db->delete('leave_requests', ['id' => $id]);
            $this->session->set_flashdata('success', 'Pengajuan cuti berhasil dibatalkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membatalkan: Data tidak ditemukan atau sudah diproses atasan.');
        }

        redirect('leave/my_leave');
    }
}
