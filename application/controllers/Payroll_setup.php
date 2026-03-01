<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_setup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        if(!in_array($this->session->userdata('role'), ['admin', 'hrd'])) show_error('Akses ditolak.', 403);
    }

    public function index() {
        $this->load->view('layout/header');
        $this->load->view('payroll/setup_list');
        $this->load->view('layout/footer');
    }

    public function get_employees_json() {
        $this->db->select('e.id, e.nip, e.full_name, e.basic_salary, u.name as unit_name, p.name as position_name');
        $this->db->from('employees e');
        $this->db->join('master_units u', 'e.unit_id = u.id', 'left');
        $this->db->join('master_positions p', 'e.position_id = p.id', 'left');
        $this->db->where_in('e.status_employee', ['permanent', 'contract', 'intern']);
        $employees = $this->db->get()->result();

        $data = [];
        $no = 1;
        
        foreach($employees as $e) {
            $basic = $e->basic_salary ? 'Rp ' . number_format($e->basic_salary, 0, ',', '.') : '<span class="text-red-500 font-bold text-[10px] uppercase tracking-wider">Belum Disetup</span>';
            
            // Count custom components assigned to this employee
            $comp_count = $this->db->where('employee_id', $e->id)->count_all_results('employee_payroll_components');
            
            if($comp_count > 0) {
                $comp_badge = '<span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-[10px] font-bold">'.$comp_count.' Komponen Khusus</span>';
            } else {
                $comp_badge = '<span class="px-2 py-0.5 rounded bg-gray-100 text-gray-500 text-[10px] font-bold">Default Master</span>';
            }

            $btn = '<button onclick="setupEmp('.$e->id.')" class="p-1.5 px-3 rounded-lg border border-blue-500 text-blue-600 hover:bg-blue-50 transition-all font-bold flex items-center justify-center gap-1 text-xs"><span class="material-symbols-outlined !text-[16px]">tune</span> Atur Gaji</button>';

            $data[] = [
                $no++,
                '<div class="flex flex-col"><span class="font-bold text-gray-900">'.$e->full_name.'</span><span class="text-xs text-gray-500">NIP: '.$e->nip.'</span></div>',
                '<div class="flex flex-col"><span class="text-sm font-medium text-gray-800">'.($e->position_name ?: '-').'</span><span class="text-[10px] font-black uppercase text-gray-400">'.($e->unit_name ?: '-').'</span></div>',
                '<span class="font-mono font-bold">'.$basic.'</span>',
                $comp_badge,
                $btn
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function get_employee_setup($emp_id) {
        // Get Employee Basic details
        $this->db->select('id, nip, full_name, basic_salary');
        $emp = $this->db->get_where('employees', ['id' => $emp_id])->row();

        // Get Master Components
        $masters = $this->db->get_where('master_payroll_components', ['is_active' => 1])->result();

        // Get Employee Overrides
        $emp_comps = $this->db->get_where('employee_payroll_components', ['employee_id' => $emp_id])->result();
        
        // Map overrides by component_id
        $overrides = [];
        $adhoc = [];
        foreach($emp_comps as $ec) {
            if($ec->component_id) {
                $overrides[$ec->component_id] = $ec;
            } else {
                $adhoc[] = $ec;
            }
        }

        // Build array combining master defaults with overrides
        $configs = [];
        foreach($masters as $m) {
            $has_override = isset($overrides[$m->id]);
            $configs[] = [
                'component_id' => $m->id,
                'name' => $m->name,
                'type' => $m->type, // allowance / deduction
                'calculation_basis' => $m->calculation_basis,
                'default_amount' => $m->amount,
                'is_active_for_emp' => $has_override ? 1 : 0,
                'custom_amount' => $has_override ? $overrides[$m->id]->amount : $m->amount
            ];
        }

        echo json_encode([
            'employee' => $emp,
            'components' => $configs,
            'adhoc' => $adhoc
        ]);
    }

    public function save_setup() {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $emp_id = $this->input->post('employee_id');
        $basic_salary = str_replace(['Rp', '.', ',', ' '], '', $this->input->post('basic_salary'));
        
        // Save basic salary
        $this->db->where('id', $emp_id)->update('employees', ['basic_salary' => $basic_salary]);

        // Process components
        $components = $this->input->post('components');
        $custom_amounts = $this->input->post('custom_amount');

        $this->db->trans_start();

        // Clear existing overrides for this employee
        $this->db->where('employee_id', $emp_id)->delete('employee_payroll_components');

        if(is_array($components) && !empty($components)) {
            $inserts = [];
            foreach($components as $comp_id) {
                // If checkbox is checked, save it to the overriding table
                $amount = str_replace(['Rp', '.', ',', ' '], '', $custom_amounts[$comp_id] ?? 0);
                
                $inserts[] = [
                    'employee_id' => $emp_id,
                    'component_id' => $comp_id,
                    'amount' => $amount,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            if(!empty($inserts)) {
                $this->db->insert_batch('employee_payroll_components', $inserts);
            }
        }

        // Process Ad-hoc Custom components
        $adhoc_names = $this->input->post('adhoc_name');
        $adhoc_types = $this->input->post('adhoc_type');
        $adhoc_bases = $this->input->post('adhoc_basis');
        $adhoc_amounts = $this->input->post('adhoc_amount');

        if(is_array($adhoc_names) && !empty($adhoc_names)) {
            $adhoc_inserts = [];
            foreach($adhoc_names as $i => $name) {
                if(trim($name) == '') continue;
                $amount = str_replace(['Rp', '.', ',', ' '], '', $adhoc_amounts[$i] ?? 0);
                if($amount <= 0) continue;
                
                $adhoc_inserts[] = [
                    'employee_id' => $emp_id,
                    'component_id' => NULL,
                    'name' => trim($name),
                    'type' => $adhoc_types[$i],
                    'calculation_basis' => $adhoc_bases[$i],
                    'amount' => $amount,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            if(!empty($adhoc_inserts)) {
                $this->db->insert_batch('employee_payroll_components', $adhoc_inserts);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan konfigurasi penggajian.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Konfigurasi gaji pegawai berhasil disimpan.']);
        }
    }
}
