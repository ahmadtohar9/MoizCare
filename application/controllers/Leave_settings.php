<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_settings extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') !== 'admin') redirect('dashboard');
    }

    public function index()
    {
        $data['leave_types'] = $this->db->get('leave_types')->result();
        $this->load->view('layout/header');
        $this->load->view('leave/settings', $data);
        $this->load->view('layout/footer');
    }

    public function quotas()
    {
        $this->db->select('e.id, e.full_name, e.nip, d.name as unit_name, e.unit_id');
        $this->db->from('employees e');
        $this->db->join('master_units d', 'e.unit_id = d.id', 'left');
        $data['employees'] = $this->db->get()->result();
        $data['leave_types'] = $this->db->get('leave_types')->result();
        $data['departments'] = $this->db->get('master_units')->result();

        $this->load->view('layout/header');
        $this->load->view('leave/quotas', $data);
        $this->load->view('layout/footer');
    }

    public function get_employee_quotas($employee_id)
    {
        $quotas = $this->db->get_where('leave_quotas', [
            'employee_id' => $employee_id, 
            'year' => date('Y')
        ])->result();
        echo json_encode($quotas);
    }

    public function save_quota()
    {
        $employee_id = $this->input->post('employee_id');
        $type_id = $this->input->post('leave_type_id');
        $total = $this->input->post('total_quota');

        $where = ['employee_id' => $employee_id, 'leave_type_id' => $type_id, 'year' => date('Y')];
        $exists = $this->db->get_where('leave_quotas', $where)->row();

        if ($exists) {
            $this->db->where('id', $exists->id)->update('leave_quotas', ['total_quota' => $total, 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            $this->db->insert('leave_quotas', array_merge($where, ['total_quota' => $total, 'created_at' => date('Y-m-d H:i:s')]));
        }

        echo json_encode(['status' => 'success']);
    }
}
