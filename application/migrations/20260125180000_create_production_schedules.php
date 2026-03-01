<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Production_Schedules extends CI_Migration {

    public function up()
    {
        // Table for Final/Production Schedules
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'shift_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE), // NULL = OFF
            'date' => array('type' => 'DATE'),
            'created_by' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE), // Karu/Admin ID
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key(['employee_id', 'date'], FALSE); // Optimization
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (shift_id) REFERENCES master_shifts(id) ON DELETE SET NULL');
        $this->dbforge->create_table('schedules', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_table('schedules', TRUE);
    }
}
