<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Leave_System_Tables extends CI_Migration {

    public function up()
    {
        // 1. Leave Types Table
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
            'default_quota' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
            'needs_attachment' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('leave_types');

        // Seed initial leave types
        $leave_types = [
            ['name' => 'Cuti Tahunan', 'default_quota' => 12, 'needs_attachment' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Sakit (Dengan SKD)', 'default_quota' => 0, 'needs_attachment' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Cuti Melahirkan', 'default_quota' => 90, 'needs_attachment' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Izin Alasan Penting', 'default_quota' => 0, 'needs_attachment' => 0, 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->insert_batch('leave_types', $leave_types);

        // 2. Employee Leave Quotas Table
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'leave_type_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'year' => array('type' => 'INT', 'constraint' => 4),
            'total_quota' => array('type' => 'INT', 'constraint' => 11),
            'used_quota' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE');
        $this->dbforge->create_table('leave_quotas');

        // 3. Leave Requests Table
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'leave_type_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'start_date' => array('type' => 'DATE'),
            'end_date' => array('type' => 'DATE'),
            'total_days' => array('type' => 'INT', 'constraint' => 11),
            'reason' => array('type' => 'TEXT'),
            'attachment' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE),
            'status' => array('type' => 'ENUM("pending", "approved_karu", "approved", "rejected")', 'default' => 'pending'),
            'karu_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'hrd_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
            'karu_note' => array('type' => 'TEXT', 'null' => TRUE),
            'hrd_note' => array('type' => 'TEXT', 'null' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE');
        $this->dbforge->create_table('leave_requests');
    }

    public function down()
    {
        $this->dbforge->drop_table('leave_requests');
        $this->dbforge->drop_table('leave_quotas');
        $this->dbforge->drop_table('leave_types');
    }
}
