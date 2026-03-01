<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends CI_Controller {

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
        // Get Employees with Stats
        $this->db->select('e.id, e.nip, e.full_name, e.photo, u.name as unit_name, p.name as position_name');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('master_positions p', 'e.position_id = p.id', 'left');
        $employees = $this->db->get()->result();

        foreach($employees as &$emp) {
            $emp->doc_count = $this->db->where('employee_id', $emp->id)->count_all_results('employee_documents');
            $emp->photo_url = (!empty($emp->photo) && file_exists($emp->photo)) ? base_url($emp->photo) : 'https://ui-avatars.com/api/?name='.urlencode($emp->full_name).'&background=random&color=fff';
        }

        $data['employees'] = $employees;
        $data['doc_types'] = $this->db->get('document_types')->result(); 
        
        // Calculate global statistics
        $this->db->select('expiry_date');
        $this->db->from('employee_documents');
        $docs = $this->db->get()->result();
        
        $warning_count = 0;
        $expired_count = 0;
        $today = new DateTime();
        
        foreach($docs as $doc) {
            if(!empty($doc->expiry_date)) {
                $expiry = new DateTime($doc->expiry_date);
                if($expiry < $today) {
                    $expired_count++;
                } else {
                    $diff = $today->diff($expiry)->days;
                    if($diff <= 30) {
                        $warning_count++;
                    }
                }
            }
        }
        
        $data['warning_count'] = $warning_count;
        $data['expired_count'] = $expired_count;
        $data['units'] = $this->db->get('master_units')->result();
        
        $this->load->view('layout/header');
        $this->load->view('documents/index', $data);
        $this->load->view('layout/footer');
    }

    public function get_documents_json()
    {
        $this->db->select('employee_documents.*, employees.full_name, employees.nip, document_types.name as type_name, document_types.has_expiry');
        $this->db->from('employee_documents');
        $this->db->join('employees', 'employee_documents.employee_id = employees.id');
        $this->db->join('document_types', 'employee_documents.document_type_id = document_types.id', 'left');
        $docs = $this->db->get()->result();

        $data = [];
        $no = 1;
        foreach($docs as $row) {
            // Calculate Status from expiry_date
            $status = '-';
            if($row->has_expiry && !empty($row->expiry_date)) {
                $today = new DateTime();
                $expiry = new DateTime($row->expiry_date);
                $interval = $today->diff($expiry);
                
                if($expiry < $today) {
                    $status = '<span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">EXPIRED</span>';
                } elseif($interval->days <= 30) {
                    $status = '<span class="px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">'.$interval->days.' Hari Lagi</span>';
                } else {
                    $status = '<span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Aktif</span>';
                }
            }

            // Expiry Check
            $expiry = $row->expiry_date ? date('d M Y', strtotime($row->expiry_date)) : '-';
            
            // Actions
            $actions = '<div class="flex items-center gap-2 justify-end">
                <a href="'.base_url($row->file_path).'" target="_blank" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all" title="View"><span class="material-symbols-outlined !text-[18px]">visibility</span></a>
                <button onclick="deleteDocument('.$row->id.')" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all" title="Delete"><span class="material-symbols-outlined !text-[18px]">delete</span></button>
            </div>';

            $data[] = [
                $no++,
                '<div class="flex flex-col"><span class="font-bold text-[#111418] dark:text-white">'.($row->document_number ?: 'Dokumen').'</span><span class="text-xs text-gray-500">File: '.basename($row->file_path).'</span></div>',
                '<div class="flex flex-col"><span class="font-bold text-gray-700 dark:text-gray-300">'.$row->full_name.'</span><span class="text-xs text-gray-500">NIP: '.$row->nip.'</span></div>',
                $row->type_name ?: 'Lainnya',
                $expiry,
                $status,
                $actions
            ];
        }
        echo json_encode(['data' => $data]);
    }

    // --- MASTER DOCUMENT TYPES CRUD ---
    public function types()
    {
        $this->load->view('layout/header');
        $this->load->view('documents/types');
        $this->load->view('layout/footer');
    }

    public function get_types_json()
    {
        $rows = $this->db->order_by('sort_order', 'ASC')->get('document_types')->result();
        $data = [];
        $no = 1;
        foreach($rows as $row) {
            $mandatory = $row->is_mandatory ? '<span class="px-2 py-1 rounded bg-red-50 text-red-600 text-[10px] font-black uppercase">Wajib</span>' : '<span class="px-2 py-1 rounded bg-gray-50 text-gray-400 text-[10px] font-bold uppercase">Opsional</span>';
            $expiry = $row->has_expiry ? '<span class="px-2 py-1 rounded bg-amber-50 text-amber-600 text-[10px] font-black uppercase">Tracked</span>' : '<span class="text-gray-300 text-[10px]">-</span>';
            
            $app_map = [
                'all' => '<span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold">Semua Pegawai</span>',
                'non-medical' => '<span class="bg-orange-50 text-orange-600 px-2 py-1 rounded text-[10px] font-bold">Non-Medis</span>',
                'medical' => '<span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-[10px] font-bold">Medis (Perw/Bid)</span>',
                'doctor' => '<span class="bg-purple-50 text-purple-600 px-2 py-1 rounded text-[10px] font-bold">Dokter</span>'
            ];

            $btn = '<div class="text-right flex justify-end gap-1">
                <button onclick="editType('.$row->id.')" class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition-all" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                <button onclick="deleteType('.$row->id.')" class="text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-all" title="Hapus"><span class="material-symbols-outlined text-[18px]">delete</span></button>
            </div>';

            $data[] = [
                'DT_RowId' => $row->id,
                $no++,
                '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-gray-400 cursor-move handle">drag_indicator</span> <span class="font-bold text-[#111418] dark:text-white">'.$row->name.'</span></div>',
                $row->category,
                $app_map[$row->applicable_to] ?? $row->applicable_to,
                $mandatory,
                $expiry,
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get_type_detail($id) {
        $data = $this->db->get_where('document_types', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function store_type()
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         $id = $this->input->post('id');
         $data = [
             'name' => $this->input->post('name'),
             'category' => $this->input->post('category'),
             'applicable_to' => $this->input->post('applicable_to'),
             'is_mandatory' => $this->input->post('is_mandatory') ? 1 : 0,
             'has_expiry' => $this->input->post('has_expiry') ? 1 : 0,
             'updated_at' => date('Y-m-d H:i:s')
         ];

         if(!empty($id)) {
             $this->db->where('id', $id);
             $this->db->update('document_types', $data);
             echo json_encode(['status' => 'success', 'message' => 'Jenis dokumen diupdate!']);
         } else {
             $data['created_at'] = date('Y-m-d H:i:s');
             $this->db->insert('document_types', $data);
             echo json_encode(['status' => 'success', 'message' => 'Jenis dokumen ditambahkan!']);
         }
    }

    public function delete_type($id)
    {
         if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
         // Check if used first
         $used = $this->db->get_where('employee_documents', ['document_type_id' => $id])->num_rows();
         if($used > 0) {
             echo json_encode(['status' => 'error', 'message' => 'Tidak bisa dihapus, tipe ini sudah digunakan oleh berkas pegawai.']);
         } else {
             $this->db->delete('document_types', ['id' => $id]);
             echo json_encode(['status' => 'success', 'message' => 'Tipe dokumen dihapus.']);
         }
    }

    public function update_type_order() {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $ids = $this->input->post('ids');
        if($ids) {
            foreach($ids as $index => $id) {
                $this->db->where('id', $id);
                $this->db->update('document_types', ['sort_order' => $index + 1]);
            }
            echo json_encode(['status' => 'success', 'message' => 'Urutan berhasil disimpan!']);
        }
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $config['upload_path']   = './uploads/documents/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size']      = 5048; // 5MB
        $config['encrypt_name']  = TRUE;

        if (!is_dir('uploads/documents')) {
            mkdir('./uploads/documents', 0777, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_upload')) {
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
        } else {
            $file_data = $this->upload->data();
            
            // Determine Status
            $status = 'valid';
            if($this->input->post('expiry_date')) {
                $expiry = $this->input->post('expiry_date');
                if(strtotime($expiry) < time()) {
                    $status = 'expired';
                } elseif(strtotime($expiry) < strtotime('+30 days')) {
                    $status = 'warning';
                }
            }

            $data = [
                'employee_id'   => $this->input->post('employee_id'),
                'document_name' => $this->input->post('document_name'),
                'document_type' => $this->input->post('document_type'), // Now ID from master table
                'expiry_date'   => $this->input->post('expiry_date') ?: NULL,
                'file_path'     => 'uploads/documents/' . $file_data['file_name'],
                'status'        => $status,
                'created_at'    => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert('employee_documents', $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Dokumen berhasil diupload!']);
            } else {
                 echo json_encode(['status' => 'error', 'message' => 'Database Insert Failed']);
            }
        }
    }

    public function delete($id)
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        
        $doc = $this->db->get_where('employee_documents', ['id' => $id])->row();
        if($doc) {
            if(file_exists($doc->file_path)) {
                unlink($doc->file_path);
            }
            $this->db->delete('employee_documents', ['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Dokumen dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Dokumen tidak ditemukan']);
        }
    }
}
