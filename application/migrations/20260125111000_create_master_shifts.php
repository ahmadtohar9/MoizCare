<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Master_Shifts extends CI_Migration {

    public function up()
    {
        // 1. Master Shifts Table
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => '50'),
            'start_time' => array('type' => 'TIME'),
            'end_time' => array('type' => 'TIME'),
            'color' => array('type' => 'VARCHAR', 'constraint' => '10', 'default' => '#3b82f6'),
            'description' => array('type' => 'TEXT', 'null' => TRUE),
            'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('master_shifts');

        // Seed default shifts
        $shifts = array(
            [
                'name' => 'Morning Shift',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'color' => '#10b981', // Green
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Evening Shift',
                'start_time' => '15:00:00',
                'end_time' => '23:00:00',
                'color' => '#f59e0b', // Amber
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '23:00:00',
                'end_time' => '07:00:00',
                'color' => '#6366f1', // Indigo
                'created_at' => date('Y-m-d H:i:s')
            ]
        );
        $this->db->insert_batch('master_shifts', $shifts);
        
        // 2. Add shift_id to employees table or create a mapping?
        // Let's create a mapping for weekly default shifts for now
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'day_of_week' => array('type' => 'ENUM("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")'),
            'shift_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (shift_id) REFERENCES master_shifts(id) ON DELETE CASCADE');
        $this->dbforge->create_table('employee_weekly_shifts');
    }

    public function down()
    {
        $this->dbforge->drop_table('employee_weekly_shifts');
        $this->dbforge->drop_table('master_shifts');
    }
}
