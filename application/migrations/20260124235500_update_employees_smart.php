<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_Employees_Smart extends CI_Migration {

    public function up()
    {
        // List of desired columns
        $columns = [
            'nik' => ['type' => 'VARCHAR', 'constraint' => '16', 'null' => TRUE, 'after' => 'full_name'],
            'place_of_birth' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE, 'after' => 'full_name'],
            'date_of_birth' => ['type' => 'DATE', 'null' => TRUE, 'after' => 'place_of_birth'],
            'religion' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE, 'after' => 'gender'],
            'marital_status' => ['type' => 'ENUM("single", "married", "widow", "widower")', 'default' => 'single', 'after' => 'religion'],
            'address_ktp' => ['type' => 'TEXT', 'null' => TRUE, 'after' => 'marital_status'],
            'address_domicile' => ['type' => 'TEXT', 'null' => TRUE, 'after' => 'address_ktp'],
            'mothers_name' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => TRUE, 'after' => 'address_domicile'],
            'npwp' => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE, 'after' => 'email'],
            'bpjs_kesehatan' => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE, 'after' => 'npwp'],
            'bpjs_ketenagakerjaan' => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE, 'after' => 'bpjs_kesehatan'],
            'resign_date' => ['type' => 'DATE', 'null' => TRUE, 'after' => 'join_date'],
            'resign_reason' => ['type' => 'TEXT', 'null' => TRUE, 'after' => 'resign_date'],
        ];

        foreach ($columns as $name => $def) {
            if (!$this->db->field_exists($name, 'employees')) {
                $this->dbforge->add_column('employees', [$name => $def]);
            }
        }

        // Modify status (direct query is safer)
        $this->db->query("ALTER TABLE employees MODIFY COLUMN status_employee ENUM('permanent', 'contract', 'intern', 'probation', 'resigned', 'terminated') DEFAULT 'contract'");

        // Create Family Table if not exists
        if (!$this->db->table_exists('employee_families')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'name' => array('type' => 'VARCHAR', 'constraint' => '150'),
                'relation' => array('type' => 'ENUM("spouse", "child", "father", "mother", "sibling")', 'default' => 'child'),
                'gender' => array('type' => 'ENUM("L", "P")', 'default' => 'L'),
                'date_of_birth' => array('type' => 'DATE', 'null' => TRUE),
                'education' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
                'job' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
                'is_dependent' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
                'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
            $this->dbforge->create_table('employee_families');
        }
    }

    public function down()
    {
        // Safe to keep columns or drop if strictly needed
    }
}
