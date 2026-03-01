<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Attendance_Table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'date' => array('type' => 'DATE'),
            'clock_in' => array('type' => 'TIME', 'null' => TRUE),
            'clock_out' => array('type' => 'TIME', 'null' => TRUE),
            'status' => array('type' => 'ENUM("present", "late", "absent", "leave")', 'default' => 'present'),
            'notes' => array('type' => 'TEXT', 'null' => TRUE),
            'location_lat' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
            'location_long' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->create_table('attendance');
    }

    public function down()
    {
        $this->dbforge->drop_table('attendance');
    }
}
