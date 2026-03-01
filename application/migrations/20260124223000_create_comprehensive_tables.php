<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Comprehensive_Tables extends CI_Migration {

    public function up()
    {
        // 1. Drop existing tables if they exist to rebuild from scratch
        $this->dbforge->drop_table('user_tokens', TRUE); // If exists
        $this->dbforge->drop_table('employees', TRUE);
        $this->dbforge->drop_table('users', TRUE);
        $this->dbforge->drop_table('master_units', TRUE);
        $this->dbforge->drop_table('master_positions', TRUE);

        // 2. Master Units Table
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
            'type' => array('type' => 'ENUM("medical", "non-medical")', 'default' => 'non-medical'),
            'description' => array('type' => 'TEXT', 'null' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('master_units');

        // Seed Units
        $units = array(
            ['name' => 'Management', 'type' => 'non-medical', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Clinical Operations', 'type' => 'medical', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Pharmacy', 'type' => 'medical', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Radiology', 'type' => 'medical', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'IT Department', 'type' => 'non-medical', 'created_at' => date('Y-m-d H:i:s')],
        );
        $this->db->insert_batch('master_units', $units);

        // 3. Master Positions Table
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
            'level' => array('type' => 'INT', 'constraint' => 2, 'default' => 1), // 1: Staff, 5: Manager, 10: Director
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('master_positions');

        // Seed Positions
        $positions = array(
            ['name' => 'Director', 'level' => 10, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'General Practitioner', 'level' => 5, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Senior Nurse', 'level' => 4, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'System Administrator', 'level' => 5, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Admin Staff', 'level' => 1, 'created_at' => date('Y-m-d H:i:s')],
        );
        $this->db->insert_batch('master_positions', $positions);

        // 4. Users Table (Authentication)
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'username' => array('type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE),
            'password' => array('type' => 'VARCHAR', 'constraint' => '255'),
            'role' => array('type' => 'ENUM("admin", "user")', 'default' => 'user'),
            'last_login' => array('type' => 'DATETIME', 'null' => TRUE),
            'status' => array('type' => 'ENUM("active", "inactive")', 'default' => 'active'),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users');

        // 5. Employees Table (Main Profile)
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'user_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE), // Relasi ke users
            
            // Identitas
            'nip' => array('type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE),
            'nik' => array('type' => 'VARCHAR', 'constraint' => '20', 'unique' => TRUE, 'null' => TRUE), // KTP
            'full_name' => array('type' => 'VARCHAR', 'constraint' => '150'),
            'gender' => array('type' => 'ENUM("L", "P")', 'null' => TRUE),
            'birth_place' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
            'birth_date' => array('type' => 'DATE', 'null' => TRUE),
            'address' => array('type' => 'TEXT', 'null' => TRUE),
            'phone' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE),
            'email' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
            'avatar_url' => array('type' => 'TEXT', 'null' => TRUE),

            // Kepegawaian
            'unit_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'position_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'status_employee' => array('type' => 'ENUM("permanent", "contract", "intern")', 'default' => 'contract'),
            'join_date' => array('type' => 'DATE', 'null' => TRUE),

            // Legalitas & Medis (Standard RS)
            'no_str' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
            'str_expiry' => array('type' => 'DATE', 'null' => TRUE),
            'no_sip' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
            'sip_expiry' => array('type' => 'DATE', 'null' => TRUE),
            
            // Timestamps
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (unit_id) REFERENCES master_units(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (position_id) REFERENCES master_positions(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->dbforge->create_table('employees');

        // Seed Data: Admin User & Employee
        // 1. Create User
        $admin_data = array(
            'username' => 'admin',
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('users', $admin_data);
        $user_id = $this->db->insert_id();

        // 2. Create Employee Profile for Admin
        $employee_data = array(
            'user_id' => $user_id,
            'nip' => 'ADM-001',
            'full_name' => 'Super Administrator',
            'unit_id' => 5, // IT
            'position_id' => 4, // System Admin
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('employees', $employee_data);
    }

    public function down()
    {
        $this->dbforge->drop_table('employees');
        $this->dbforge->drop_table('users');
        $this->dbforge->drop_table('master_units');
        $this->dbforge->drop_table('master_positions');
    }
}
