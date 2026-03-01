<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function get_user_by_nip($nip)
    {
        // Join users and employees table
        $this->db->select('users.id as user_id, users.username, users.password, users.role, users.status, employees.*, master_units.name as unit_name, master_positions.name as position_name');
        $this->db->from('users');
        $this->db->join('employees', 'employees.user_id = users.id', 'left');
        $this->db->join('master_units', 'employees.unit_id = master_units.id', 'left');
        $this->db->join('master_positions', 'employees.position_id = master_positions.id', 'left');
        
        $this->db->group_start();
        $this->db->where('employees.nip', $nip);
        $this->db->or_where('users.username', $nip);
        $this->db->group_end();
        
        return $this->db->get()->row();
    }

    public function get_all_users()
    {
        $this->db->select('employees.*, master_units.name as department, master_positions.name as position');
        $this->db->from('employees');
        $this->db->join('master_units', 'employees.unit_id = master_units.id', 'left');
        $this->db->join('master_positions', 'employees.position_id = master_positions.id', 'left');
        return $this->db->get()->result();
    }

    public function count_all_users()
    {
        return $this->db->count_all('employees');
    }
}
