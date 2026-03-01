<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function roster()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        $data['shifts'] = $this->db->get('master_shifts')->result();
        $data['units'] = $this->db->get('master_units')->result();

        // Get employees for filtering
        $this->db->select('e.id, e.full_name, e.nip, u.name as unit_name');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        
        if($role === 'karu') {
            $this->db->where('e.unit_id', $emp->unit_id);
        }
        
        $data['employees'] = $this->db->get()->result();

        $this->load->view('layout/header');
        $this->load->view('schedule/roster', $data);
        $this->load->view('layout/footer');
    }

    public function get_roster_json()
    {
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $unit_id = $this->input->get('unit_id');
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        $this->db->select('s.*, e.full_name, ms.name as shift_name, ms.color as shift_color, ms.start_time, ms.end_time');
        $this->db->from('schedules s');
        $this->db->join('employees e', 's.employee_id = e.id');
        $this->db->join('master_shifts ms', 's.shift_id = ms.id');
        
        if ($start) $this->db->where('s.date >=', $start);
        if ($end) $this->db->where('s.date <=', $end);
        
        if ($role === 'karu') {
            $this->db->where('e.unit_id', $emp->unit_id);
        } elseif ($unit_id) {
            $this->db->where('e.unit_id', $unit_id);
        }

        $rows = $this->db->get()->result();
        
        $events = [];
        foreach($rows as $r) {
            $events[] = [
                'id' => $r->id,
                'title' => "[" . substr($r->shift_name, 0, 1) . "] " . $r->full_name,
                'start' => $r->date,
                'color' => $r->shift_color ?: '#3b82f6',
                'extendedProps' => [
                    'employee_id' => $r->employee_id,
                    'shift_id' => $r->shift_id,
                    'shift_name' => $r->shift_name,
                    'time' => date('H:i', strtotime($r->start_time)) . ' - ' . date('H:i', strtotime($r->end_time))
                ]
            ];
        }
        echo json_encode($events);
    }

    public function save_assignment()
    {
        $employee_id = $this->input->post('employee_id');
        $shift_id = $this->input->post('shift_id');
        $date = $this->input->post('date');
        $user_id = $this->session->userdata('user_id');

        if (!$employee_id || !$date) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
            return;
        }

        // Check if assignment exists
        $existing = $this->db->get_where('schedules', [
            'employee_id' => $employee_id,
            'date' => $date
        ])->row();

        $data = [
            'employee_id' => $employee_id,
            'shift_id' => $shift_id ?: NULL,
            'date' => $date,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            if (empty($shift_id)) {
                $this->db->delete('schedules', ['id' => $existing->id]);
            } else {
                $this->db->where('id', $existing->id)->update('schedules', $data);
            }
        } elseif (!empty($shift_id)) {
            $data['created_by'] = $user_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('schedules', $data);
        }

        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diperbarui']);
    }

    public function approvals() {
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        // Get all pending/relevant submissions
        $this->db->select('ss.*, e.full_name as employee_name, e.nip as employee_nip, u.name as unit_name');
        $this->db->from('schedule_submissions ss');
        $this->db->join('employees e', 'ss.employee_id = e.id');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        
        // If karu, only see unit's submissions
        if($role === 'karu') {
            $this->db->where('e.unit_id', $emp->unit_id);
        }

        $this->db->order_by('ss.submitted_at', 'DESC');
        $data['submissions'] = $this->db->get()->result();

        $this->load->view('layout/header');
        $this->load->view('schedule/approvals', $data);
        $this->load->view('layout/footer');
    }

    public function get_submission_full_detail($id) {
        $this->db->select('ss.*, e.full_name, e.photo, e.nip');
        $this->db->from('schedule_submissions ss');
        $this->db->join('employees e', 'ss.employee_id = e.id');
        $this->db->where('ss.id', $id);
        $submission = $this->db->get()->row();
        
        $details = $this->db->select('sd.*, ms.name as shift_name, ms.color, ms.start_time, ms.end_time')
                        ->from('schedule_submission_details sd')
                        ->join('master_shifts ms', 'sd.shift_id = ms.id', 'left')
                        ->where('sd.submission_id', $id)
                        ->get()->result();

        echo json_encode([
            'submission' => $submission,
            'details' => $details
        ]);
    }

    public function process_approval() {
        $id = $this->input->post('id');
        $status = $this->input->post('status'); // approved or revision
        $note = $this->input->post('note');
        $user_id = $this->session->userdata('user_id');

        $this->db->trans_start();

        $submission = $this->db->get_where('schedule_submissions', ['id' => $id])->row();

        $update = [
            'status' => $status,
            'revision_note' => ($status === 'revision' ? $note : NULL),
            'approved_at' => ($status === 'approved' ? date('Y-m-d H:i:s') : NULL),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $id)->update('schedule_submissions', $update);

        // If approved, sync to PRODUCTION TABLE (schedules)
        if ($status === 'approved') {
            // 1. Delete existing for THIS employee in THIS period
            $start_date = date("Y-m-01", strtotime("{$submission->period_year}-{$submission->period_month}-01"));
            $end_date   = date("Y-m-t", strtotime($start_date));
            
            $this->db->where('employee_id', $submission->employee_id);
            $this->db->where("date >=", $start_date);
            $this->db->where("date <=", $end_date);
            $this->db->delete('schedules');

            // 2. Insert new from details
            $details = $this->db->get_where('schedule_submission_details', ['submission_id' => $id])->result();
            $batch = [];
            foreach ($details as $d) {
                if ($d->shift_id) {
                    $batch[] = [
                        'employee_id' => $submission->employee_id,
                        'shift_id' => $d->shift_id,
                        'date' => $d->date,
                        'created_by' => $user_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
            if (!empty($batch)) {
                $this->db->insert_batch('schedules', $batch);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses persetujuan.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Status Roster berhasil diperbarui!']);
        }
    }

    public function delete_assignment($id)
    {
        $this->db->delete('schedules', ['id' => $id]);
        echo json_encode(['status' => 'success']);
    }
}
