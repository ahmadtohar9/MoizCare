<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Logo_To_Settings extends CI_Migration {

    public function up()
    {
        $fields = array(
            'company_logo' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'after' => 'company_name'),
        );
        $this->dbforge->add_column('settings', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('settings', 'company_logo');
    }
}
