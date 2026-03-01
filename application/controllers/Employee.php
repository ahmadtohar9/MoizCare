<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth');
		}
	}

    public function index()
	{
        // Get Master Data for Dropdowns (still needed for Modal)
        $data['units'] = $this->db->get('master_units')->result();
        $data['positions'] = $this->db->get('master_positions')->result();
		
		$this->load->view('layout/header');
		$this->load->view('employee/index', $data);
		$this->load->view('layout/footer');
	}

    public function get_employees_json()
    {
        $employees = $this->User_model->get_all_users();
        $data = [];
        $no = 1;
        foreach($employees as $row) {
            $photo_src = (!empty($row->photo) && file_exists($row->photo)) ? base_url($row->photo) : 'https://ui-avatars.com/api/?name='.urlencode($row->full_name).'&background=0D8ABC&color=fff';
            $avatar = '<img class="object-cover w-full h-full" src="'.$photo_src.'">';
            
            $btn = '<div class="flex items-center gap-2">
                    <a href="'.base_url('employee_documents/index/'.$row->id).'" class="p-1.5 rounded-lg bg-green-500 text-white hover:bg-green-600 transition-all flex items-center justify-center" title="Dokumen">
                        <span class="material-symbols-outlined !text-[18px]">folder</span>
                    </a>
                    <button onclick="viewEmployee('.$row->id.')" class="p-1.5 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-all flex items-center justify-center" title="Lihat Detail">
                        <span class="material-symbols-outlined !text-[18px]">visibility</span>
                    </button>
                    <button onclick="editEmployee('.$row->id.')" class="p-1.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition-all flex items-center justify-center" title="Edit">
                        <span class="material-symbols-outlined !text-[18px]">edit</span>
                    </button>
                    <button onclick="deleteEmployee('.$row->id.')" class="p-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-all flex items-center justify-center" title="Nonaktifkan">
                        <span class="material-symbols-outlined !text-[18px]">person_off</span>
                    </button>
                </div>';

            $data[] = [
                $no++,
                '<div class="size-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700">'.$avatar.'</div>',
                '<div class="flex flex-col"><span class="text-sm font-bold text-[#111418] dark:text-white">'.$row->full_name.'</span><span class="text-xs text-gray-500">'.$row->nip.'@moizcare.com</span></div>',
                $row->nip,
                '<span class="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold">'.$row->position.'</span>',
                '<span class="text-sm text-gray-600 dark:text-gray-400">'.$row->department.'</span>',
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get_full_employee_detail($id)
    {
        // Get Employee with Joined Names
        $this->db->select('e.*, u.name as unit_name, p.name as position_name');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('master_positions p', 'e.position_id = p.id', 'left');
        $this->db->where('e.id', $id);
        $emp = $this->db->get()->row();

        // Get Family
        $families = $this->db->get_where('employee_families', ['employee_id' => $id])->result();

        // Get Documents
        $this->db->select('ed.*, dt.name as type_name');
        $this->db->from('employee_documents ed');
        $this->db->join('document_types dt', 'ed.document_type_id = dt.id', 'left');
        $this->db->where('ed.employee_id', $id);
        $this->db->order_by('ed.uploaded_at', 'DESC');
        $documents = $this->db->get()->result();

        // Get Photo
        $emp->photo_url = (!empty($emp->photo) && file_exists($emp->photo)) ? base_url($emp->photo) : 'https://ui-avatars.com/api/?name='.urlencode($emp->full_name).'&background=0D8ABC&color=fff&size=256';

        // Get Recent Attendance (Last 30 days)
        $this->db->select('a.*, ms.name as shift_name, ms.start_time, ms.end_time');
        $this->db->from('attendance a');
        $this->db->join('master_shifts ms', 'a.shift_id = ms.id', 'left');
        $this->db->where('a.employee_id', $id);
        $this->db->order_by('a.date', 'DESC');
        $this->db->limit(30);
        $attendances = $this->db->get()->result();

        // Get Schedule (from 7 days ago to next 7 days)
        $date_past = date('Y-m-d', strtotime('-7 days'));
        $date_future = date('Y-m-d', strtotime('+14 days'));
        
        $this->db->select('sd.date, ms.name as shift_name, ms.start_time, ms.end_time, ms.color');
        $this->db->from('schedule_submission_details sd');
        $this->db->join('schedule_submissions ss', 'sd.submission_id = ss.id');
        $this->db->join('master_shifts ms', 'sd.shift_id = ms.id');
        $this->db->where('ss.employee_id', $id);
        $this->db->where('ss.status', 'approved');
        $this->db->where('sd.date >=', $date_past);
        $this->db->where('sd.date <=', $date_future);
        $this->db->order_by('sd.date', 'DESC');
        $schedules = $this->db->get()->result();

        echo json_encode([
            'employee' => $emp, 
            'families' => $families,
            'documents' => $documents,
            'attendances' => $attendances,
            'schedules' => $schedules
        ]);
    }

    public function store()
    {
        // AJAX Only
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $this->load->library('form_validation');
        $id = $this->input->post('id');
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required');
        
        $nip = $this->input->post('nip');
        $nip_rule = 'required';
        if(empty($id)) { 
             // Insert: Must be unique
             $nip_rule .= '|is_unique[employees.nip]';
        } else {
             // Edit: Only check unique if NIP is different from current
             $current = $this->db->get_where('employees', ['id' => $id])->row();
             if($current && $current->nip != $nip) {
                 $nip_rule .= '|is_unique[employees.nip]';
             }
        }
        $this->form_validation->set_rules('nip', 'NIP', $nip_rule, ['is_unique' => 'NIP <b>'.$nip.'</b> sudah terdaftar di sistem!']);
        $this->form_validation->set_rules('unit_id', 'Departemen', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        } else {
            // Data for 'employees' table
            $employee_data = [
                'nip' => $this->input->post('nip'),
                'full_name' => $this->input->post('full_name'),
                'unit_id' => $this->input->post('unit_id'),
                'position_id' => $this->input->post('position_id') ?: NULL,
                'status_employee' => $this->input->post('status_employee'),
                'join_date' => $this->input->post('join_date') ?: NULL,
                'nik' => $this->input->post('nik'),
                'place_of_birth' => $this->input->post('place_of_birth'),
                'date_of_birth' => $this->input->post('date_of_birth') ?: NULL,
                'gender' => $this->input->post('gender'),
                'religion' => $this->input->post('religion'),
                'marital_status' => $this->input->post('marital_status'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address_ktp' => $this->input->post('address_ktp'),
                'address_domicile' => $this->input->post('address_domicile'),
                'mothers_name' => $this->input->post('mothers_name'),
                'npwp' => $this->input->post('npwp'),
                'bpjs_kesehatan' => $this->input->post('bpjs_kesehatan'),
                'bpjs_ketenagakerjaan' => $this->input->post('bpjs_ketenagakerjaan'),
                'resign_date' => $this->input->post('resign_date') ?: NULL,
                'resign_reason' => $this->input->post('resign_reason'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle Photo Upload
            if (!empty($_FILES['photo']['name'])) {
                $config['upload_path']   = './uploads/employees/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['max_size']      = 2048; // 2MB
                $config['encrypt_name']  = TRUE;

                if (!is_dir('uploads/employees')) mkdir('./uploads/employees', 0777, true);

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('photo')) {
                    $uploadData = $this->upload->data();
                    $employee_data['photo'] = 'uploads/employees/' . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return; // Stop execution
                }
            }

            if(!empty($id)) {
                // UPDATE
                $this->db->where('id', $id);
                if ($this->db->update('employees', $employee_data)) {
                     echo json_encode(['status' => 'success', 'message' => 'Data Pegawai berhasil diupdate!', 'id' => $id]);
                } else {
                     echo json_encode(['status' => 'error', 'message' => 'Gagal update data pegawai.']);
                }
            } else {
                // INSERT - ONLY Employee Data (Registration is separate)
                $employee_data['created_at'] = date('Y-m-d H:i:s');

                if ($this->db->insert('employees', $employee_data)) {
                     $new_id = $this->db->insert_id();
                     echo json_encode(['status' => 'success', 'message' => 'Pegawai baru berhasil ditambahkan!', 'id' => $new_id]);
                } else {
                     echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data pegawai.']);
                }
            }
        }
    }
    
    // --- FAMILY MANAGEMENT ---
    public function get_family_json($emp_id)
    {
        $rows = $this->db->get_where('employee_families', ['employee_id' => $emp_id])->result();
        $data = [];
        $no = 1;
        foreach($rows as $row) {
            $relation_map = [
                'spouse' => 'Suami/Istri', 'child' => 'Anak', 'father' => 'Ayah', 'mother' => 'Ibu', 'sibling' => 'Saudara'
            ];
            $tanggungan = $row->is_dependent ? '<span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">Ditanggung</span>' : '<span class="text-gray-500 text-xs">-</span>';

            $btn = '<div class="flex justify-end gap-1">
                <button type="button" onclick="editFamily('.$row->id.')" class="text-amber-500 hover:bg-amber-50 p-1 rounded-lg transition-all" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                <button type="button" onclick="deleteFamily('.$row->id.')" class="text-red-500 hover:bg-red-50 p-1 rounded-lg transition-all" title="Hapus"><span class="material-symbols-outlined text-[18px]">delete</span></button>
            </div>';

            $data[] = [
                $no++,
                $row->name . '<br><small class="text-gray-500">'.$row->gender.'</small>',
                $relation_map[$row->relation] ?? $row->relation,
                $row->date_of_birth ? date('d-m-Y', strtotime($row->date_of_birth)) : '-',
                $row->education,
                $row->job,
                $tanggungan,
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get_family_detail($id)
    {
        $data = $this->db->get_where('employee_families', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function store_family()
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         
         $id = $this->input->post('id');
         $data = [
             'employee_id' => $this->input->post('employee_id'),
             'name' => $this->input->post('name'),
             'relation' => $this->input->post('relation'),
             'gender' => $this->input->post('gender'),
             'date_of_birth' => $this->input->post('date_of_birth') ?: NULL,
             'education' => $this->input->post('education'),
             'job' => $this->input->post('job'),
             'is_dependent' => $this->input->post('is_dependent'),
         ];

         if(!empty($id)) {
             // UPDATE
             if ($this->db->where('id', $id)->update('employee_families', $data)) {
                 echo json_encode(['status' => 'success', 'message' => 'Data Keluarga diupdate!']);
             } else {
                 echo json_encode(['status' => 'error', 'message' => 'Gagal Update DB.']);
             }
         } else {
             // INSERT
             $data['created_at'] = date('Y-m-d H:i:s');
             if ($this->db->insert('employee_families', $data)) {
                 echo json_encode(['status' => 'success', 'message' => 'Anggota Keluarga ditambahkan!']);
             } else {
                 echo json_encode(['status' => 'error', 'message' => 'Gagal Insert DB.']);
             }
         }
    }

    public function delete_family($id)
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         $this->db->delete('employee_families', ['id' => $id]);
         echo json_encode(['status' => 'success', 'message' => 'Data dihapus.']);
    }

    public function edit_employee($id)
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $data = $this->db->get_where('employees', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function delete_employee($id)
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         
         // Get User ID to delete login access
         $employee = $this->db->get_where('employees', ['id' => $id])->row();
         if ($employee) {
             $this->db->delete('users', ['id' => $employee->user_id]);
             $this->db->delete('employees', ['id' => $id]);
             echo json_encode(['status' => 'success', 'message' => 'Pegawai berhasil dihapus.']);
         } else {
             echo json_encode(['status' => 'error', 'message' => 'Data pegawai tidak ditemukan.']);
         }
    }

    // --- POSITIONS MANAGEMENT ---
    public function positions()
    {
        $this->load->view('layout/header');
        $this->load->view('employee/positions');
        $this->load->view('layout/footer');
    }

    public function get_positions_json()
    {
        $positions = $this->db->get('master_positions')->result();
        $data = [];
        $no = 1;
        foreach($positions as $row) {
            $data[] = [
                $no++,
                '<span class="font-bold text-[#111418] dark:text-white">'.$row->name.'</span>',
                '<span class="text-sm text-gray-600 dark:text-gray-400">Level '.$row->level.'</span>',
                '<div class="text-right flex items-center justify-end gap-2">
                    <button onclick="editItem('.$row->id.')" class="text-amber-500 hover:bg-amber-50 p-2 rounded-lg transition-all" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <button onclick="deleteItem('.$row->id.')" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-all" title="Hapus"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                </div>'
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get_position_detail($id)
    {
        $data = $this->db->get_where('master_positions', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function store_position()
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         
         $id = $this->input->post('id');

         $data = [
             'name' => $this->input->post('name'),
             'level' => $this->input->post('level'),
         ];

         if(!empty($id)) {
             // UPDATE
             if ($this->db->where('id', $id)->update('master_positions', $data)) {
                 echo json_encode(['status' => 'success', 'message' => 'Jabatan berhasil diupdate!']);
             } else {
                 echo json_encode(['status' => 'error', 'message' => 'Gagal DB Error.']);
             }
         } else {
             // INSERT
             $data['created_at'] = date('Y-m-d H:i:s');
             if ($this->db->insert('master_positions', $data)) {
                 echo json_encode(['status' => 'success', 'message' => 'Jabatan berhasil ditambahkan!']);
             } else {
                 echo json_encode(['status' => 'error', 'message' => 'Gagal DB Error.']);
             }
         }
    }

    public function delete_position($id)
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         $this->db->delete('master_positions', ['id' => $id]);
         echo json_encode(['status' => 'success', 'message' => 'Jabatan dihapus.']);
    }

    // --- DEPARTMENTS MANAGEMENT ---
    public function departments()
    {
        $this->load->view('layout/header');
        $this->load->view('employee/departments');
        $this->load->view('layout/footer');
    }

    public function get_departments_json()
    {
        $units = $this->db->get('master_units')->result();
        $data = [];
        $no = 1;
        foreach($units as $row) {
            $type_badge = ($row->type == 'medical') ? 
                '<span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">Medis</span>' : 
                '<span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">Non-Medis</span>';

            $data[] = [
                $no++,
                '<div class="flex flex-col"><span class="font-bold text-[#111418] dark:text-white">'.$row->name.'</span><span class="text-xs text-gray-500">'.$row->description.'</span></div>',
                $type_badge,
                '<div class="text-right flex items-center justify-end gap-2">
                    <button onclick="editItem('.$row->id.')" class="text-amber-500 hover:bg-amber-50 p-2 rounded-lg transition-all" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <button onclick="deleteItem('.$row->id.')" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-all" title="Hapus"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                </div>'
            ];
        }
        echo json_encode(['data' => $data]);
    }
    
    public function get_department_detail($id)
    {
        $data = $this->db->get_where('master_units', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function store_department()
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

         $id = $this->input->post('id');

         $data = [
             'name' => $this->input->post('name'),
             'type' => $this->input->post('type'),
             'description' => $this->input->post('description'),
         ];

         if(!empty($id)) {
             // UPDATE
             if ($this->db->where('id', $id)->update('master_units', $data)) {
                 echo json_encode(['status' => 'success', 'message' => 'Departemen berhasil diupdate!']);
             } else {
                 echo json_encode(['status' => 'error', 'message' => 'Gagal DB Error.']);
             }
         } else {
             // INSERT
             $data['created_at'] = date('Y-m-d H:i:s');
             if ($this->db->insert('master_units', $data)) {
                 echo json_encode(['status' => 'success', 'message' => 'Departemen berhasil ditambahkan!']);
             } else {
                 echo json_encode(['status' => 'error', 'message' => 'Gagal DB Error.']);
             }
         }
    }

    public function delete_department($id)
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         $this->db->delete('master_units', ['id' => $id]);
         echo json_encode(['status' => 'success', 'message' => 'Departemen dihapus.']);
    }

    public function print_pdf($id) 
    {
        // 1. Get Employee Data
        $this->db->select('e.*, u.name as unit_name, p.name as position_name');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('master_positions p', 'e.position_id = p.id', 'left');
        $this->db->where('e.id', $id);
        $data['employee'] = $this->db->get()->row();
        
        if (!$data['employee']) show_404();

        // 2. Get Family
        $data['family'] = $this->db->get_where('employee_families', ['employee_id' => $id])->result();

        // 3. Get Documents Summary
        $this->db->select('ed.*, dt.name as doc_type');
        $this->db->from('employee_documents ed');
        $this->db->join('document_types dt', 'ed.document_type_id = dt.id', 'left');
        $this->db->where('ed.employee_id', $id);
        $data['documents'] = $this->db->get()->result();

        // Load View
        $html = $this->load->view('employee/profile_pdf', $data, TRUE);
        
        // Use mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        // Add Watermark
        $mpdf->SetWatermarkText('MOIZ CARE');
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.05;

        // Set Title & Author
        $mpdf->SetTitle('Profil Pegawai - ' . $data['employee']->full_name);
        $mpdf->SetAuthor('HRIS Moizcare');

        $mpdf->WriteHTML($html);
        $mpdf->Output('Profil_Pegawai_' . $data['employee']->nip . '.pdf', 'I');
    }
}
