<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Self_schedule extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $employee_id = $this->session->userdata('employee_id');
        $role = $this->session->userdata('role');

        if(!$employee_id) {
            if($role === 'admin') {
                // Admin bypass: Pick the first employee to show the interface
                $first_emp = $this->db->get('employees')->row();
                if($first_emp) $employee_id = $first_emp->id;
                else show_error('Gagal memuat preview: Belum ada data pegawai di sistem.');
            } else {
                show_error('Hanya akun Pegawai yang bisa mengakses fitur ini.');
            }
        }

        // Get Employee Info
        $this->db->select('e.*, u.name as unit_name, u.type as unit_type');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->where('e.id', $employee_id);
        $data['my_info'] = $this->db->get()->row();

        // Get Potential Karu (Only users with role 'karu')
        $this->db->select('e.id, e.full_name, e.nip');
        $this->db->from('employees e');
        $this->db->join('users u', 'e.user_id = u.id');
        $this->db->where('u.role', 'karu');
        $this->db->where('e.id !=', $employee_id);
        $data['approvers'] = $this->db->get()->result();

        // Get Current Submission if any for this month
        $data['current_month'] = date('n');
        $data['current_year'] = date('Y');
        
        $this->db->where(['employee_id' => $employee_id, 'period_month' => $data['current_month'], 'period_year' => $data['current_year']]);
        $data['submission'] = $this->db->get('schedule_submissions')->row();

        $data['shifts'] = $this->db->get('master_shifts')->result();

        $this->load->view('layout/header');
        $this->load->view('self_schedule/index', $data);
        $this->load->view('layout/footer');
    }

    public function submit_schedule() {
        $employee_id = $this->input->post('employee_id');
        $role = $this->session->userdata('role');
        
        // Security check: only admins can submit for others
        if($role !== 'admin' || empty($employee_id)) {
            $employee_id = $this->session->userdata('employee_id');
        }

        $approver_id = $this->input->post('approver_id');
        $shifts = $this->input->post('shifts'); 
        $month = date('n');
        $year = date('Y');

        if(!$employee_id) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal: ID Pegawai tidak terdeteksi. Silakan Login ulang.']);
            return;
        }

        if(empty($approver_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Mohon pilih Kepala Ruangan (Karu) terlebih dahulu.']);
            return;
        }

        $this->db->trans_start();

        $existing = $this->db->get_where('schedule_submissions', [
            'employee_id' => $employee_id,
            'period_month' => $month,
            'period_year' => $year
        ])->row();

        $data = [
            'employee_id' => $employee_id,
            'approver_id' => $approver_id,
            'period_month' => $month,
            'period_year' => $year,
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if($existing) {
            if($existing->status === 'approved' && $role !== 'admin') {
                echo json_encode(['status' => 'error', 'message' => 'Jadwal sudah disetujui dan terkunci.']);
                return;
            }
            $this->db->where('id', $existing->id)->update('schedule_submissions', $data);
            $submission_id = $existing->id;
            $this->db->delete('schedule_submission_details', ['submission_id' => $submission_id]);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('schedule_submissions', $data);
            $submission_id = $this->db->insert_id();
        }

        $details = [];
        if(!empty($shifts)) {
            foreach($shifts as $date => $shift_ids) {
                if(is_array($shift_ids)) {
                    foreach($shift_ids as $sid) {
                        if(!empty($sid)) {
                            $details[] = [
                                'submission_id' => $submission_id,
                                'date' => $date,
                                'shift_id' => $sid
                            ];
                        }
                    }
                }
            }
            if(!empty($details)) {
                $this->db->insert_batch('schedule_submission_details', $details);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan database saat menyimpan jadwal.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dikirim untuk approval!']);
        }
    }

    public function log_view() {
        // View for historical submissions
        $employee_id = $this->session->userdata('employee_id');
        $this->db->order_by('period_year DESC, period_month DESC');
        $data['history'] = $this->db->get_where('schedule_submissions', ['employee_id' => $employee_id])->result();
        
        $this->load->view('layout/header');
        // We'll add this view later
        $this->load->view('self_schedule/history', $data);
        $this->load->view('layout/footer');
    }
}
