<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Photo_Employees extends CI_Migration {

    public function up()
    {
        $fields = array(
            'photo' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'after' => 'email'
            ),
        );
        if (!$this->db->field_exists('photo', 'employees')) {
            $this->dbforge->add_column('employees', $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column('employees', 'photo');
    }
}
