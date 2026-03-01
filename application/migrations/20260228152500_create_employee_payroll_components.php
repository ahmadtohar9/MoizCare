<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_employee_payroll_components extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'employee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'component_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (component_id) REFERENCES master_payroll_components(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->dbforge->create_table('employee_payroll_components');
    }

    public function down()
    {
        $this->dbforge->drop_table('employee_payroll_components');
    }
}
