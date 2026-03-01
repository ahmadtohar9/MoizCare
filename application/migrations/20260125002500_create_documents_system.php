<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Documents_System extends CI_Migration {

    public function up()
    {
        // 1. Create document_types table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Nama Jenis Dokumen'
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
                'comment' => 'Kategori (Pribadi, Kepegawaian, Sertifikasi, dll)'
            ],
            'has_expiry' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Apakah dokumen ini punya masa berlaku?'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('document_types', TRUE);

        // 2. Create employee_documents table
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
                'unsigned' => TRUE
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

        // 3. Insert default document types
        $default_types = [
            ['name' => 'KTP', 'category' => 'Pribadi', 'has_expiry' => 0, 'description' => 'Kartu Tanda Penduduk'],
            ['name' => 'KK (Kartu Keluarga)', 'category' => 'Pribadi', 'has_expiry' => 0, 'description' => 'Kartu Keluarga'],
            ['name' => 'NPWP', 'category' => 'Pribadi', 'has_expiry' => 0, 'description' => 'Nomor Pokok Wajib Pajak'],
            ['name' => 'Ijazah', 'category' => 'Pendidikan', 'has_expiry' => 0, 'description' => 'Ijazah Pendidikan'],
            ['name' => 'Transkrip Nilai', 'category' => 'Pendidikan', 'has_expiry' => 0, 'description' => 'Transkrip Nilai'],
            ['name' => 'STR (Surat Tanda Registrasi)', 'category' => 'Sertifikasi', 'has_expiry' => 1, 'description' => 'Surat Tanda Registrasi Tenaga Kesehatan'],
            ['name' => 'SIP (Surat Izin Praktik)', 'category' => 'Sertifikasi', 'has_expiry' => 1, 'description' => 'Surat Izin Praktik'],
            ['name' => 'Sertifikat Pelatihan', 'category' => 'Sertifikasi', 'has_expiry' => 1, 'description' => 'Sertifikat Pelatihan/Workshop'],
            ['name' => 'BPJS Kesehatan', 'category' => 'Kepegawaian', 'has_expiry' => 0, 'description' => 'Kartu BPJS Kesehatan'],
            ['name' => 'BPJS Ketenagakerjaan', 'category' => 'Kepegawaian', 'has_expiry' => 0, 'description' => 'Kartu BPJS Ketenagakerjaan'],
            ['name' => 'Surat Lamaran', 'category' => 'Kepegawaian', 'has_expiry' => 0, 'description' => 'Surat Lamaran Kerja'],
            ['name' => 'CV', 'category' => 'Kepegawaian', 'has_expiry' => 0, 'description' => 'Curriculum Vitae'],
            ['name' => 'Kontrak Kerja', 'category' => 'Kepegawaian', 'has_expiry' => 1, 'description' => 'Kontrak Kerja/PKWT'],
            ['name' => 'SKCK', 'category' => 'Pribadi', 'has_expiry' => 1, 'description' => 'Surat Keterangan Catatan Kepolisian'],
        ];

        foreach ($default_types as $type) {
            $type['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('document_types', $type);
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('employee_documents', TRUE);
        $this->dbforge->drop_table('document_types', TRUE);
    }
}
