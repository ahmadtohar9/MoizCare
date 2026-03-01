<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	public function index()
	{
		if (!$this->session->userdata('logged_in')) {
			redirect('auth');
		}
		
		$role = $this->session->userdata('role');
		$user_id = $this->session->userdata('user_id');

		if ($role === 'admin') {
			$this->_admin_dashboard();
		} elseif ($role === 'karu') {
			$this->_karu_dashboard($user_id);
		} else {
			$this->_staff_dashboard($user_id);
		}
	}

	private function _admin_dashboard()
	{
		$today = date('Y-m-d');

		// 1. Core Summary Metrics
		$data['total_employees'] = $this->db->count_all('employees');
		$this->db->where('date', $today);
		$data['attendance_today'] = $this->db->count_all_results('attendance');
		$this->db->where('status', 'pending');
		$data['pending_leaves'] = $this->db->count_all_results('leave_requests');

		// 2. Document Monitoring
		$warning_date = date('Y-m-d', strtotime('+30 days'));
		$this->db->where('expiry_date <', $today);
		$data['expired_docs_count'] = $this->db->count_all_results('employee_documents');
		$this->db->where('expiry_date >=', $today);
		$this->db->where('expiry_date <=', $warning_date);
		$data['expiring_soon_count'] = $this->db->count_all_results('employee_documents');

		// 3. Attendance Trends (Last 14 Days)
		$trends = [];
		for ($i = 13; $i >= 0; $i--) {
			$date = date('Y-m-d', strtotime("-$i days"));
			$count = $this->db->where('date', $date)->count_all_results('attendance');
			$trends[] = ['label' => date('d M', strtotime($date)), 'count' => $count];
		}
		$data['attendance_trends'] = $trends;

		// 4. Recent Activity
		$this->db->select('ed.*, e.full_name, dt.name as doc_type');
		$this->db->from('employee_documents ed');
		$this->db->join('employees e', 'ed.employee_id = e.id');
		$this->db->join('document_types dt', 'ed.document_type_id = dt.id');
		$this->db->order_by('ed.uploaded_at', 'DESC');
		$this->db->limit(5);
		$data['recent_activities'] = $this->db->get()->result();

		// 5. Check-in Exceptions
		$this->db->select('al.*, e.full_name, e.photo');
		$this->db->from('attendance al');
		$this->db->join('employees e', 'al.employee_id = e.id');
		$this->db->where('al.date', $today);
		$this->db->where('al.status', 'late');
		$this->db->limit(3);
		$data['late_checkins'] = $this->db->get()->result();

		// Top 5 Upcoming Expiries
		$this->db->select('ed.*, e.full_name, dt.name as type_name');
		$this->db->from('employee_documents ed');
		$this->db->join('employees e', 'ed.employee_id = e.id');
		$this->db->join('document_types dt', 'ed.document_type_id = dt.id');
		$this->db->where('ed.expiry_date IS NOT NULL');
		$this->db->where('ed.expiry_date >=', $today);
		$this->db->order_by('ed.expiry_date', 'ASC');
		$this->db->limit(5);
		$data['upcoming_expiries'] = $this->db->get()->result();

		// 6. Department Composition for Chart
		$this->db->select('u.name, COUNT(e.id) as count');
		$this->db->from('employees e');
		$this->db->join('master_units u', 'e.unit_id = u.id', 'left');
		$this->db->group_by('e.unit_id');
		$data['dept_composition'] = $this->db->get()->result();

		$this->load->view('layout/header');
		$this->load->view('dashboard/overview', $data);
		$this->load->view('layout/footer');
	}

	private function _karu_dashboard($user_id)
	{
		$today = date('Y-m-d');
		$emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();
		
		if (!$emp) show_error('Profil pegawai tidak ditemukan.');

		// Stats for Unit
		$data['unit_name'] = $this->db->get_where('master_units', ['id' => $emp->unit_id])->row()->name;
		
		$this->db->where('unit_id', $emp->unit_id);
		$data['total_unit_staff'] = $this->db->count_all_results('employees');

		$this->db->select('COUNT(attendance.id) as count');
		$this->db->from('attendance');
		$this->db->join('employees', 'attendance.employee_id = employees.id');
		$this->db->where('employees.unit_id', $emp->unit_id);
		$this->db->where('attendance.date', $today);
		$data['unit_attendance_today'] = $this->db->get()->row()->count;

		$this->db->select('COUNT(lr.id) as count');
		$this->db->from('leave_requests lr');
		$this->db->join('employees e', 'lr.employee_id = e.id');
		$this->db->where('e.unit_id', $emp->unit_id);
		$this->db->where('lr.status', 'pending');
		$data['unit_pending_leave'] = $this->db->get()->row()->count;

		$this->load->view('layout/header');
		$this->load->view('dashboard/karu_dashboard', $data);
		$this->load->view('layout/footer');
	}

	private function _staff_dashboard($user_id)
	{
		$today = date('Y-m-d');
		$emp = $this->db->get_where('employees', ['user_id' => $user_id])->row();
		
		if (!$emp) show_error('Profil pegawai tidak ditemukan.');

		$data['employee'] = $emp;
		$data['unit'] = $this->db->get_where('master_units', ['id' => $emp->unit_id])->row();
		
		// Get today's attendance log if exists
		$data['attendance'] = $this->db->get_where('attendance', [
			'employee_id' => $emp->id,
			'date' => $today
		])->row();

		// Get today's shift from schedule
		$this->db->select('s.*, ms.name as shift_name, ms.start_time, ms.end_time');
		$this->db->from('schedules s');
		$this->db->join('master_shifts ms', 's.shift_id = ms.id');
		$this->db->where('s.employee_id', $emp->id);
		$this->db->where('s.date', $today);
		$data['today_shift'] = $this->db->get()->row();

		$this->load->view('layout/header');
		$this->load->view('dashboard/staff_dashboard', $data);
		$this->load->view('layout/footer');
	}
}
