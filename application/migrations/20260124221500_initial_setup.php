<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Initial_setup extends CI_Migration {

    public function up()
    {
        // Users Table
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'nip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => TRUE,
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'full_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'role' => array(
                'type' => 'ENUM("admin", "executive", "staff", "doctor", "nurse")',
                'default' => 'staff',
            ),
            'department' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'position' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'avatar_url' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'status' => array(
                'type' => 'ENUM("active", "inactive")',
                'default' => 'active',
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users');

        // Seed Tables
        $data = array(
            array(
                'nip' => 'admin',
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'full_name' => 'Super Admin',
                'role' => 'admin',
                'department' => 'IT',
                'position' => 'System Administrator',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'nip' => '123456',
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'full_name' => 'Sarah Jenkins',
                'role' => 'nurse',
                'department' => 'Clinical Ops',
                'position' => 'Senior Nurse',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ),
             array(
                'nip' => 'exec',
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'full_name' => 'Executive Manager',
                'role' => 'executive',
                'department' => 'Management',
                'position' => 'Director',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            )
        );
        $this->db->insert_batch('users', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}
