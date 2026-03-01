<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_documents extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    // View: Halaman Dokumen Pegawai (Dual Tabs)
    public function index($employee_id) {
        $data['employee_id'] = $employee_id;
        $data['employee'] = $this->db->get_where('employees', ['id' => $employee_id])->row();
        
        // Determine employee category (non-medical, medical, doctor)
        $this->db->select('master_units.type as unit_type');
        $this->db->from('employees');
        $this->db->join('master_units', 'employees.unit_id = master_units.id');
        $this->db->where('employees.id', $employee_id);
        $emp_data = $this->db->get()->row();
        
        $category = 'non-medical'; // default
        if($emp_data && $emp_data->unit_type == 'medical') {
            // Check if doctor or nurse
            $position = strtolower($data['employee']->position_id ?? '');
            $category = (strpos($position, 'dokter') !== false) ? 'doctor' : 'medical';
        }
        $data['employee_category'] = $category;
        
        // Get Mandatory Documents for this category
        $this->db->where('is_mandatory', 1);
        $this->db->where_in('applicable_to', ['all', $category]);
        $this->db->order_by('sort_order', 'ASC');
        $mandatory_types = $this->db->get('document_types')->result();
        
        // Check which ones are uploaded
        $mandatory_docs = [];
        $uploaded_count = 0;
        
        foreach($mandatory_types as $type) {
            $this->db->select('employee_documents.*, document_types.has_expiry');
            $this->db->from('employee_documents');
            $this->db->join('document_types', 'employee_documents.document_type_id = document_types.id');
            $this->db->where('employee_documents.employee_id', $employee_id);
            $this->db->where('employee_documents.document_type_id', $type->id);
            $this->db->where('employee_documents.is_supporting', 0);
            $uploaded = $this->db->get()->row();
            
            $doc_item = (object)[
                'type_id' => $type->id,
                'name' => $type->name,
                'has_expiry' => $type->has_expiry,
                'uploaded' => $uploaded ? true : false
            ];
            
            if($uploaded) {
                $doc_item->doc_id = $uploaded->id;
                $doc_item->file_path = $uploaded->file_path;
                $doc_item->expiry_date = $uploaded->expiry_date;
                $doc_item->uploaded_at = $uploaded->uploaded_at;
                $uploaded_count++;
            }
            
            $mandatory_docs[] = $doc_item;
        }
        
        $data['mandatory_docs'] = $mandatory_docs;
        $data['mandatory_total'] = count($mandatory_types);
        $data['uploaded_count'] = $uploaded_count;
        
        // Get Supporting Document Types (non-mandatory)
        $data['supporting_types'] = $this->db->where('is_mandatory', 0)->get('document_types')->result();
        
        // Count supporting docs
        $data['supporting_total'] = $this->db->where('employee_id', $employee_id)->where('is_supporting', 1)->count_all_results('employee_documents');
        
        $this->load->view('layout/header', $data);
        $this->load->view('employee_documents/index', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: Get Supporting Documents JSON
    public function get_supporting_json($employee_id) {
        try {
            $this->db->select('ed.*, dt.name as type_name, dt.has_expiry');
            $this->db->from('employee_documents ed');
            $this->db->join('document_types dt', 'ed.document_type_id = dt.id', 'left');
            $this->db->where('ed.employee_id', $employee_id);
            $this->db->where('ed.is_supporting', 1);
            $this->db->order_by('ed.uploaded_at', 'DESC');
            $docs = $this->db->get()->result();

            $data = [];
            $no = 1;
            foreach($docs as $doc) {
                // Status Expired
                $status = '-';
                if($doc->has_expiry && !empty($doc->expiry_date)) {
                    $today = new DateTime();
                    $expiry = new DateTime($doc->expiry_date);
                    $interval = $today->diff($expiry);
                    
                    if($expiry < $today) {
                        $status = '<span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">EXPIRED</span>';
                    } elseif($interval->days <= 30) {
                        $status = '<span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">'.$interval->days.' Hari Lagi</span>';
                    } else {
                        $status = '<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Aktif</span>';
                    }
                }

                $btn = '<div class="flex gap-1">
                    <a href="'.base_url($doc->file_path).'" target="_blank" class="p-1.5 rounded-lg bg-blue-500 text-white hover:bg-blue-600" title="Lihat"><span class="material-symbols-outlined !text-[16px]">visibility</span></a>
                    <button type="button" onclick="deleteSupporting('.$doc->id.')" class="p-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600" title="Hapus"><span class="material-symbols-outlined !text-[16px]">delete</span></button>
                </div>';

                $data[] = [
                    $no++,
                    $doc->type_name,
                    $doc->document_number ?: '-',
                    $doc->issue_date ? date('d M Y', strtotime($doc->issue_date)) : '-',
                    $doc->expiry_date ? date('d M Y', strtotime($doc->expiry_date)) : '-',
                    $status,
                    $btn
                ];
            }

            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    // Upload/Store document (both mandatory & supporting)
    public function store() {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $id = $this->input->post('id');
        $employee_id = $this->input->post('employee_id');

        // Handle file upload
        $file_path = null;
        if (!empty($_FILES['file']['name'])) {
            $config['upload_path'] = './uploads/documents/';
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['max_size'] = 5120; // 5MB
            $config['encrypt_name'] = TRUE;

            if (!is_dir('uploads/documents')) mkdir('./uploads/documents', 0777, true);

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $file_path = 'uploads/documents/' . $uploadData['file_name'];
            } else {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        $data = [
            'employee_id' => $employee_id,
            'document_type_id' => $this->input->post('document_type_id'),
            'is_supporting' => $this->input->post('is_supporting'),
            'document_number' => $this->input->post('document_number'),
            'issue_date' => $this->input->post('issue_date') ?: NULL,
            'expiry_date' => $this->input->post('expiry_date') ?: NULL,
            'issuer' => $this->input->post('issuer'),
            'notes' => $this->input->post('notes'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($file_path) {
            $data['file_path'] = $file_path;
        }

        if (!empty($id)) {
            // Update
            $this->db->where('id', $id);
            if ($this->db->update('employee_documents', $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Dokumen berhasil diupdate!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal update dokumen.']);
            }
        } else {
            // Insert
            $data['uploaded_at'] = date('Y-m-d H:i:s');
            if ($this->db->insert('employee_documents', $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Dokumen berhasil diupload!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal upload dokumen.']);
            }
        }
    }

    // Delete document
    public function delete($id) {
        $doc = $this->db->get_where('employee_documents', ['id' => $id])->row();
        if ($doc && file_exists($doc->file_path)) {
            unlink($doc->file_path);
        }
        if ($this->db->delete('employee_documents', ['id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Dokumen berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal hapus dokumen.']);
        }
    }
}
