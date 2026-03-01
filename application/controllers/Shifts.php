<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shifts extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['shifts'] = $this->db->get('master_shifts')->result();
        $this->db->select('e.id, e.full_name, e.nip, u.name as unit_name, u.type as unit_type');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $data['employees'] = $this->db->get()->result();

        $this->load->view('layout/header');
        $this->load->view('shifts/index', $data);
        $this->load->view('layout/footer');
    }

    public function get_shifts_json() {
        $shifts = $this->db->get('master_shifts')->result();
        echo json_encode(['data' => $shifts]);
    }

    public function store_shift() {
        $id = $this->input->post('id');
        $data = [
            'name' => $this->input->post('name'),
            'start_time' => $this->input->post('start_time'),
            'end_time' => $this->input->post('end_time'),
            'color' => $this->input->post('color'),
            'description' => $this->input->post('description'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if($id) {
            $this->db->where('id', $id)->update('master_shifts', $data);
            echo json_encode(['status' => 'success', 'message' => 'Shift updated successfully']);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('master_shifts', $data);
            echo json_encode(['status' => 'success', 'message' => 'New shift added']);
        }
    }

    public function delete_shift($id) {
        // Check if shift is used
        $used = $this->db->get_where('employee_weekly_shifts', ['shift_id' => $id])->num_rows();
        if($used > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete shift as it is assigned to employees']);
        } else {
            $this->db->delete('master_shifts', ['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Shift deleted']);
        }
    }

    // Weekly Assignment Logic
    public function get_employee_weekly_shifts($employee_id) {
        $this->db->where('employee_id', $employee_id);
        $data = $this->db->get('employee_weekly_shifts')->result();
        echo json_encode($data);
    }

    public function save_weekly_schedule() {
        $employee_id = $this->input->post('employee_id');
        $schedules = $this->input->post('schedule'); // day => shift_id

        if($employee_id && is_array($schedules)) {
            // Clear existing
            $this->db->delete('employee_weekly_shifts', ['employee_id' => $employee_id]);
            
            $data = [];
            foreach($schedules as $day => $shift_id) {
                if(!empty($shift_id)) {
                    $data[] = [
                        'employee_id' => $employee_id,
                        'day_of_week' => $day,
                        'shift_id' => $shift_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
            if(!empty($data)) {
                $this->db->insert_batch('employee_weekly_shifts', $data);
            }
            echo json_encode(['status' => 'success', 'message' => 'Weekly schedule saved!']);
        }
    }
}
