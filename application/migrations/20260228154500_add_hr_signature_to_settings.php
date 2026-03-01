<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_hr_signature_to_settings extends CI_Migration {

    public function up()
    {
        $fields = array(
            'hr_signature_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'default' => 'Bpk. Moiz Azhar',
            ),
            'hr_signature_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'default' => 'HRD & Finance Director',
            ),
        );
        $this->dbforge->add_column('settings', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('settings', 'hr_signature_name');
        $this->dbforge->drop_column('settings', 'hr_signature_title');
    }
}
