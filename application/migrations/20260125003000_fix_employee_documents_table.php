<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_Employee_Documents_Table extends CI_Migration {

    public function up()
    {
        // Drop existing table and recreate with correct structure
        $this->dbforge->drop_table('employee_documents', TRUE);
        
        // Recreate with all columns
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ],
            'document_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'comment' => 'FK to document_types'
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Path file dokumen'
            ],
            'document_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
                'comment' => 'Nomor Dokumen'
            ],
            'issue_date' => [
                'type' => 'DATE',
                'null' => TRUE,
                'comment' => 'Tanggal Terbit'
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => TRUE,
                'comment' => 'Tanggal Expired'
            ],
            'issuer' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => TRUE,
                'comment' => 'Penyelenggara/Penerbit'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Catatan'
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('employee_id');
        $this->dbforge->add_key('document_type_id');
        $this->dbforge->create_table('employee_documents', TRUE);
    }

    public function down()
    {
        // No rollback needed
    }
}
