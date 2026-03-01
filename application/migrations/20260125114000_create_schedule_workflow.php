<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Schedule_Workflow extends CI_Migration {

    public function up()
    {
        // 1. Table for Schedule Submission Header
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'approver_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE), // Karu selected by user
            'period_month' => array('type' => 'INT', 'constraint' => 2),
            'period_year' => array('type' => 'INT', 'constraint' => 4),
            'status' => array('type' => 'ENUM("draft", "pending", "revision", "approved")', 'default' => 'draft'),
            'revision_note' => array('type' => 'TEXT', 'null' => TRUE),
            'submitted_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'approved_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->create_table('schedule_submissions');

        // 2. Table for Schedule Submission Details (Daily Shifts)
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'submission_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'date' => array('type' => 'DATE'),
            'shift_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE), // Null = OFF
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (submission_id) REFERENCES schedule_submissions(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (shift_id) REFERENCES master_shifts(id) ON DELETE SET NULL');
        $this->dbforge->create_table('schedule_submission_details');
    }

    public function down()
    {
        $this->dbforge->drop_table('schedule_submission_details');
        $this->dbforge->drop_table('schedule_submissions');
    }
}
