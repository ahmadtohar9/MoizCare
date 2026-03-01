<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->library('form_validation');
	}

	public function index()
	{
		if ($this->session->userdata('logged_in')) {
			redirect('dashboard');
		}
		$this->load->view('auth/login');
	}

	public function search_employee()
	{
		$query = $this->input->post('query');
		
		// Cari di master data pegawai berdasarkan NIP atau Nama
		$this->db->select('e.full_name, e.nip, e.user_id, u.status as user_status');
		$this->db->from('employees e');
		$this->db->join('users u', 'e.user_id = u.id', 'left');
		$this->db->group_start();
		$this->db->where('e.nip', $query);
		$this->db->or_like('e.full_name', $query);
		$this->db->group_end();
		$this->db->limit(1); // Ambil yang paling mendekati
		$emp = $this->db->get()->row();

		if ($emp) {
			if ($emp->user_id && $emp->user_status === 'active') {
				echo json_encode(['status' => 'exists', 'message' => 'Halo '.$emp->full_name.'! Akun Anda sudah terdaftar & aktif. Silakan login langsung.']);
			} else if ($emp->user_id && $emp->user_status === 'pending') {
				echo json_encode(['status' => 'exists', 'message' => 'Halo '.$emp->full_name.'! Registrasi Anda sedang menunggu persetujuan Admin.']);
			} else {
				// Belum ada user_id atau user ditolak/tidak aktif
				echo json_encode(['status' => 'success', 'name' => $emp->full_name, 'nip' => $emp->nip]);
			}
		} else {
			echo json_encode(['status' => 'not_found', 'message' => 'Data Pegawai tidak ditemukan. Pastikan NIP atau Nama benar.']);
		}
	}

	public function login_process()
	{
		$this->form_validation->set_rules('nip', 'NIP', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('auth/login');
		} else {
			$nip = $this->input->post('nip');
			$password = $this->input->post('password');

			$user = $this->User_model->get_user_by_nip($nip);

			if ($user) {
				if ($user->status === 'pending') {
					$this->session->set_flashdata('error', 'Akun Anda sedang menunggu persetujuan Admin.');
					redirect('auth');
				}
				if ($user->status === 'inactive' || $user->status === 'rejected') {
					$this->session->set_flashdata('error', 'Akun Anda tidak aktif atau ditolak. Hubungi HRD.');
					redirect('auth');
				}

				if (password_verify($password, $user->password)) {
					$session_data = array(
						'user_id'   => $user->user_id,
						'username'  => $user->username,
						'full_name' => $user->full_name,
						'role'      => $user->role,
						'avatar_url'=> $user->avatar_url,
						'employee_id' => $user->id,
						'logged_in' => TRUE
					);
					$this->session->set_userdata($session_data);
					redirect('dashboard');
				} else {
					$this->session->set_flashdata('error', 'Invalid Password');
					redirect('auth');
				}
			} else {
				$this->session->set_flashdata('error', 'NIP tidak ditemukan');
				redirect('auth');
			}
		}
	}

	public function register_process()
	{
		$nip = $this->input->post('nip');
		$password = $this->input->post('password');

		if(empty($nip) || empty($password)) {
			$this->session->set_flashdata('error', 'NIP dan Password wajib diisi.');
			redirect('auth');
		}

		// 1. Check if NIP exists in employees table
		$employee = $this->db->get_where('employees', ['nip' => $nip])->row();
		if(!$employee) {
			$this->session->set_flashdata('error', 'NIP Anda belum terdaftar di data Kepegawaian. Hubungi HRD.');
			redirect('auth');
		}

		// 2. Check if this employee already has a user account
		if($employee->user_id) {
			$this->session->set_flashdata('error', 'NIP ini sudah memiliki akun. Silakan login.');
			redirect('auth');
		}

		// 3. SECURE CHECK: Check if username already exists in users table directly
		$exists = $this->db->get_where('users', ['username' => $nip])->row();
		if ($exists) {
			$this->session->set_flashdata('error', 'Username/NIP <b>'.$nip.'</b> sudah terdaftar sebagai akun sistem lain. Hubungi Admin.');
			redirect('auth');
		}

		// 4. Create User Account (Status Pending)
		$user_data = [
			'username' => $nip,
			'password' => password_hash($password, PASSWORD_BCRYPT),
			'role' => 'user',
			'status' => 'pending', // Wajib Pending agar di-approve HRD
			'created_at' => date('Y-m-d H:i:s')
		];

		$this->db->trans_start();
		$this->db->insert('users', $user_data);
		$user_id = $this->db->insert_id();

		// Link employee to this user
		$this->db->where('id', $employee->id)->update('employees', ['user_id' => $user_id]);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat registrasi.');
		} else {
			$this->session->set_flashdata('success', 'Registrasi berhasil! Mohon tunggu persetujuan Admin.');
		}
		redirect('auth');
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('auth');
	}
}
