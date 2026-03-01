<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Master_Doc_Types extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
            'description' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE),
            'is_required' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('master_document_types');

        $data = [
            ['name' => 'KTP/Identitas Diri', 'is_required' => 1],
            ['name' => 'Ijazah Terakhir', 'is_required' => 1],
            ['name' => 'STR (Surat Tanda Registrasi)', 'is_required' => 1],
            ['name' => 'SIP (Surat Izin Praktik)', 'is_required' => 1],
            ['name' => 'Sertifikat Pelatihan', 'is_required' => 0],
            ['name' => 'SKCK', 'is_required' => 0],
        ];
        $this->db->insert_batch('master_document_types', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('master_document_types');
    }
}
