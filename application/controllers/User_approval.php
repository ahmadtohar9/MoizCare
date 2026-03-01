<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_approval extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
    }

    public function index()
    {
        $this->db->select('u.id, u.username, u.status, u.role, u.created_at, e.full_name, e.nip, ut.name as unit_name');
        $this->db->from('users u');
        $this->db->join('employees e', 'e.user_id = u.id');
        $this->db->join('master_units ut', 'e.unit_id = ut.id', 'left');
        $this->db->order_by('u.status', 'ASC'); // Pending first
        $this->db->order_by('u.created_at', 'DESC');
        $data['all_users'] = $this->db->get()->result();

        $this->load->view('layout/header');
        $this->load->view('user_approval/index', $data);
        $this->load->view('layout/footer');
    }

    public function process($id, $action)
    {
        $data = [];
        
        // 1. Handle Status Change
        $status_map = [
            'approve' => 'active',
            'reject' => 'rejected',
            'block' => 'inactive',
            'activate' => 'active'
        ];

        if (isset($status_map[$action])) {
            $data['status'] = $status_map[$action];
        }

        // 2. Handle Role Update (Always check if role is sent)
        $new_role = $this->input->post('role');
        if($new_role && in_array($new_role, ['user', 'karu', 'admin'])) {
            $data['role'] = $new_role;
        }

        // 3. Database Update
        if(!empty($data)) {
            $this->db->where('id', $id)->update('users', $data);
        }
        
        $msg_map = [
            'approve' => 'User berhasil disetujui!',
            'reject' => 'User telah ditolak.',
            'block' => 'Akun berhasil dinonaktifkan.',
            'activate' => 'Akun berhasil diaktifkan kembali.',
            'update_role' => 'Role user berhasil diperbarui.'
        ];

        echo json_encode(['status' => 'success', 'message' => $msg_map[$action]]);
    }
}
