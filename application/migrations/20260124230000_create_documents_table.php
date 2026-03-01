<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Documents_Table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'employee_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
            'document_name' => array('type' => 'VARCHAR', 'constraint' => '150'),
            'document_type' => array('type' => 'ENUM("identity", "education", "certification", "legal", "contract", "other")', 'default' => 'other'),
            'file_path' => array('type' => 'VARCHAR', 'constraint' => '255'),
            'expiry_date' => array('type' => 'DATE', 'null' => TRUE),
            'status' => array('type' => 'ENUM("valid", "expired", "warning", "pending")', 'default' => 'valid'),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE');
        $this->dbforge->create_table('employee_documents');
    }

    public function down()
    {
        $this->dbforge->drop_table('employee_documents');
    }
}
