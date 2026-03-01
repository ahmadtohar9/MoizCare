<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_model extends CI_Model {

    public function generate_payroll($month, $year) {
        $this->db->trans_start();

        // 1. Create Period
        $period_data = [
            'period_month' => $month,
            'period_year'  => $year,
            'status'       => 'draft',
            'created_at'   => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payroll_periods', $period_data);
        $period_id = $this->db->insert_id();

        // 2. Get All Active Employees
        $this->db->where_in('status_employee', ['permanent', 'contract', 'probation']);
        $employees = $this->db->get('employees')->result();

        if(empty($employees)) {
            $this->db->trans_rollback();
            return false;
        }

        // 3. Setup Date Range for the given month
        $start_date = date('Y-m-d', strtotime("$year-$month-01"));
        $end_date   = date('Y-m-t', strtotime("$year-$month-01"));

        // 4. Get active payroll components from master table
        $components = $this->db->get_where('master_payroll_components', ['is_active' => 1])->result();

        foreach ($employees as $emp) {
            // Get Attendance data for this employee
            $this->db->where('employee_id', $emp->id);
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
            $attendances = $this->db->get('attendance')->result();

            $att_count = 0;
            $late_count = 0;
            $absent_count = 0; 

            foreach($attendances as $att) {
                if(in_array($att->status, ['present', 'late'])) {
                    $att_count++;
                    if($att->status == 'late') {
                        $late_count++;
                    }
                }
            }

            // Defaults (Prevent null processing)
            $basic = $emp->basic_salary > 0 ? $emp->basic_salary : 3500000; // Default UMR logic if not set

            $total_allowance = 0;
            $total_deduction = 0;
            $details = [];

            // 4. Fetch Employee Specific Components Overrides & Ad-hoc
            $this->db->select('ec.amount as custom_amount, ec.name as custom_name, ec.type as custom_type, ec.calculation_basis as custom_basis, m.*');
            $this->db->from('employee_payroll_components ec');
            $this->db->join('master_payroll_components m', 'ec.component_id = m.id', 'left');
            $this->db->where('ec.employee_id', $emp->id);
            $emp_comps = $this->db->get()->result();

            // Use Custom configuration if exists, else Default active masters
            $active_components = count($emp_comps) > 0 ? $emp_comps : $components;

            // Calculate dynamic components
            foreach($active_components as $comp) {
                // Determine which amount to use
                $comp_amount = isset($comp->custom_amount) ? $comp->custom_amount : $comp->amount;
                $c_name = !empty($comp->custom_name) ? $comp->custom_name : $comp->name;
                $c_type = !empty($comp->custom_type) ? $comp->custom_type : $comp->type;
                $c_basis = !empty($comp->custom_basis) ? $comp->custom_basis : $comp->calculation_basis;
                
                $item_amount = 0;
                
                // Basis of calculation
                if($c_basis == 'fixed_monthly') {
                    $item_amount = $comp_amount;
                } else if($c_basis == 'per_attendance') {
                    $item_amount = $comp_amount * $att_count;
                } else if($c_basis == 'per_late_day') {
                    $item_amount = $comp_amount * $late_count;
                }

                if($item_amount > 0) {
                    $desc = $c_name;
                    if($c_basis == 'per_attendance') $desc .= ' (' . $att_count . ' Hadir)';
                    if($c_basis == 'per_late_day') $desc .= ' (' . $late_count . ' Telat)';

                    $details[] = [
                        'type' => $c_type,
                        'description' => $desc,
                        'amount' => $item_amount
                    ];
                    
                    if($c_type == 'allowance') $total_allowance += $item_amount;
                    else $total_deduction += $item_amount;
                }
            }

            // Net Salary
            $net_salary = $basic + $total_allowance - $total_deduction;

            // Save Slip
            $slip_data = [
                'period_id'        => $period_id,
                'employee_id'      => $emp->id,
                'basic_salary'     => $basic,
                'total_allowance'  => $total_allowance,
                'total_deduction'  => $total_deduction,
                'net_salary'       => $net_salary,
                'attendance_count' => $att_count,
                'late_count'       => $late_count,
                'absent_count'     => $absent_count,
                'status'           => 'draft',
                'created_at'       => date('Y-m-d H:i:s')
            ];
            $this->db->insert('payroll_slips', $slip_data);
            $slip_id = $this->db->insert_id();

            // Insert details linking to slip_id
            $final_details = [];
            foreach($details as $d) {
                $final_details[] = [
                    'slip_id' => $slip_id,
                    'type' => $d['type'],
                    'description' => $d['description'],
                    'amount' => $d['amount']
                ];
            }
            
            if(!empty($final_details)) {
                $this->db->insert_batch('payroll_slip_details', $final_details);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $period_id;
    }

    public function recalculate_single($period_id, $employee_id) {
        $period = $this->db->get_where('payroll_periods', ['id' => $period_id])->row();
        $month = $period->period_month;
        $year = $period->period_year;

        $emp = $this->db->get_where('employees', ['id' => $employee_id])->row();

        $start_date = date('Y-m-d', strtotime("$year-$month-01"));
        $end_date   = date('Y-m-t', strtotime("$year-$month-01"));

        $components = $this->db->get_where('master_payroll_components', ['is_active' => 1])->result();

        // Get Attendance data
        $this->db->where('employee_id', $emp->id);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $attendances = $this->db->get('attendance')->result();

        $att_count = 0;
        $late_count = 0;
        $absent_count = 0; 

        foreach($attendances as $att) {
            if(in_array($att->status, ['present', 'late'])) {
                $att_count++;
                if($att->status == 'late') {
                    $late_count++;
                }
            }
        }

        $basic = $emp->basic_salary > 0 ? $emp->basic_salary : 3500000;
        $total_allowance = 0;
        $total_deduction = 0;
        $details = [];

        // Fetch Employee Specific Components Overrides & Ad-hoc
        $this->db->select('ec.amount as custom_amount, ec.name as custom_name, ec.type as custom_type, ec.calculation_basis as custom_basis, m.*');
        $this->db->from('employee_payroll_components ec');
        $this->db->join('master_payroll_components m', 'ec.component_id = m.id', 'left');
        $this->db->where('ec.employee_id', $emp->id);
        $emp_comps = $this->db->get()->result();

        $active_components = count($emp_comps) > 0 ? $emp_comps : $components;

        foreach($active_components as $comp) {
            $comp_amount = isset($comp->custom_amount) ? $comp->custom_amount : $comp->amount;
            $c_name = !empty($comp->custom_name) ? $comp->custom_name : $comp->name;
            $c_type = !empty($comp->custom_type) ? $comp->custom_type : $comp->type;
            $c_basis = !empty($comp->custom_basis) ? $comp->custom_basis : $comp->calculation_basis;
            
            $item_amount = 0;
            
            if($c_basis == 'fixed_monthly') {
                $item_amount = $comp_amount;
            } else if($c_basis == 'per_attendance') {
                $item_amount = $comp_amount * $att_count;
            } else if($c_basis == 'per_late_day') {
                $item_amount = $comp_amount * $late_count;
            }

            if($item_amount > 0) {
                $desc = $c_name;
                if($c_basis == 'per_attendance') $desc .= ' (' . $att_count . ' Hadir)';
                if($c_basis == 'per_late_day') $desc .= ' (' . $late_count . ' Telat)';

                $details[] = [
                    'type' => $c_type,
                    'description' => $desc,
                    'amount' => $item_amount
                ];
                
                if($c_type == 'allowance') $total_allowance += $item_amount;
                else $total_deduction += $item_amount;
            }
        }

        $net_salary = $basic + $total_allowance - $total_deduction;

        $slip_data = [
            'period_id'        => $period_id,
            'employee_id'      => $emp->id,
            'basic_salary'     => $basic,
            'total_allowance'  => $total_allowance,
            'total_deduction'  => $total_deduction,
            'net_salary'       => $net_salary,
            'attendance_count' => $att_count,
            'late_count'       => $late_count,
            'absent_count'     => $absent_count,
            'status'           => 'draft',
            'created_at'       => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payroll_slips', $slip_data);
        $slip_id = $this->db->insert_id();

        $final_details = [];
        foreach($details as $d) {
            $final_details[] = [
                'slip_id' => $slip_id,
                'type' => $d['type'],
                'description' => $d['description'],
                'amount' => $d['amount']
            ];
        }
        
        if(!empty($final_details)) {
            $this->db->insert_batch('payroll_slip_details', $final_details);
        }

        return $slip_id;
    }
}
