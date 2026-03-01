<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_components extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        if(!in_array($this->session->userdata('role'), ['admin', 'hrd'])) {
            show_error('Akses ditolak.', 403, 'Akses Ditolak');
        }
    }

    public function index() {
        $this->load->view('layout/header');
        $this->load->view('payroll/components');
        $this->load->view('layout/footer');
    }

    public function get_json() {
        $this->db->order_by('type', 'ASC');
        $this->db->order_by('name', 'ASC');
        $components = $this->db->get('master_payroll_components')->result();
        $data = [];
        $no = 1;
        
        $calc_labels = [
            'fixed_monthly' => 'Bulanan Tetap',
            'per_attendance' => 'Per Hari Hadir',
            'per_late_day' => 'Per Hari Terlambat'
        ];

        foreach($components as $c) {
            $badge_type = $c->type == 'allowance' ? 
                '<span class="text-green-700 bg-green-100 border border-green-200 px-2 py-0.5 rounded-md font-black text-[10px] uppercase tracking-wider">Tunjangan (+)</span>' : 
                '<span class="text-red-700 bg-red-100 border border-red-200 px-2 py-0.5 rounded-md font-black text-[10px] uppercase tracking-wider">Potongan (-)</span>';
            
            $calc = $calc_labels[$c->calculation_basis] ?? $c->calculation_basis;
            $status = $c->is_active ? 
                '<span class="text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase tracking-widest flex items-center justify-center gap-1 w-max"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Aktif</span>' : 
                '<span class="text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase tracking-widest flex items-center justify-center gap-1 w-max"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif</span>';

            $btn = '<div class="flex gap-2 justify-end">
                        <button onclick="editComp('.$c->id.')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit"><span class="material-symbols-outlined !text-[18px]">edit</span></button>
                        <button onclick="deleteComp('.$c->id.')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus"><span class="material-symbols-outlined !text-[18px]">delete</span></button>
                    </div>';

            $data[] = [
                $no++,
                '<span class="font-bold text-gray-900">'.$c->name.'</span>',
                $badge_type,
                '<div class="flex flex-col"><span class="text-sm font-mono font-bold text-gray-800">Rp '.number_format($c->amount, 0, ',', '.').'</span><span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">'.$calc.'</span></div>',
                $status,
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get($id) {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $comp = $this->db->get_where('master_payroll_components', ['id' => $id])->row();
        echo json_encode($comp);
    }

    public function store() {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        
        $id = $this->input->post('id');
        $amount = str_replace(['Rp', '.', ',', ' '], '', $this->input->post('amount'));
        
        $data = [
            'name' => $this->input->post('name'),
            'type' => $this->input->post('type'),
            'calculation_basis' => $this->input->post('calculation_basis'),
            'amount' => $amount ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        ];

        if($id) {
            $this->db->where('id', $id)->update('master_payroll_components', $data);
            echo json_encode(['status'=>'success', 'message'=>'Komponen berhasil diupdate.']);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('master_payroll_components', $data);
            echo json_encode(['status'=>'success', 'message'=>'Komponen berhasil ditambahkan.']);
        }
    }

    public function delete($id) {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $this->db->delete('master_payroll_components', ['id' => $id]);
        echo json_encode(['status'=>'success', 'message'=>'Komponen berhasil dihapus.']);
    }
}
