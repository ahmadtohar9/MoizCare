<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Revamp_Documents_System extends CI_Migration {

    public function up()
    {
        // 1. Add new columns to document_types
        $new_columns = [
            'is_mandatory' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Apakah dokumen ini wajib untuk akreditasi?',
                'after' => 'has_expiry'
            ],
            'applicable_to' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'all',
                'comment' => 'all, non-medical, medical, doctor',
                'after' => 'is_mandatory'
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Urutan tampilan',
                'after' => 'applicable_to'
            ]
        ];

        foreach ($new_columns as $name => $def) {
            if (!$this->db->field_exists($name, 'document_types')) {
                $this->dbforge->add_column('document_types', [$name => $def]);
            }
        }

        // 2. Add is_supporting to employee_documents
        if (!$this->db->field_exists('is_supporting', 'employee_documents')) {
            $this->dbforge->add_column('employee_documents', [
                'is_supporting' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'comment' => '0 = Berkas Wajib, 1 = Berkas Pendukung',
                    'after' => 'document_type_id'
                ]
            ]);
        }

        // 3. Clear old data and reseed with new structure
        $this->db->truncate('document_types');

        // 4. Seed Mandatory Documents
        $mandatory_docs = [
            // === PRIBADI (All) ===
            ['name' => 'KTP', 'category' => 'Pribadi', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 1],
            ['name' => 'Kartu Keluarga (KK)', 'category' => 'Pribadi', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 2],
            ['name' => 'NPWP', 'category' => 'Pribadi', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 3],
            ['name' => 'SKCK', 'category' => 'Pribadi', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 1, 'sort_order' => 4],
            
            // === PENDIDIKAN (All) ===
            ['name' => 'Ijazah Terakhir', 'category' => 'Pendidikan', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 5],
            ['name' => 'Transkrip Nilai', 'category' => 'Pendidikan', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 6],
            
            // === KEPEGAWAIAN (All) ===
            ['name' => 'Surat Lamaran', 'category' => 'Kepegawaian', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 7],
            ['name' => 'CV (Curriculum Vitae)', 'category' => 'Kepegawaian', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 8],
            ['name' => 'Pas Foto 3x4', 'category' => 'Kepegawaian', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 9],
            ['name' => 'Surat Keterangan Sehat', 'category' => 'Kepegawaian', 'is_mandatory' => 1, 'applicable_to' => 'all', 'has_expiry' => 1, 'sort_order' => 10],
            
            // === MEDIS ONLY (Perawat/Bidan) ===
            ['name' => 'STR (Surat Tanda Registrasi)', 'category' => 'Sertifikasi', 'is_mandatory' => 1, 'applicable_to' => 'medical', 'has_expiry' => 1, 'sort_order' => 11],
            ['name' => 'SIP (Surat Izin Praktik)', 'category' => 'Sertifikasi', 'is_mandatory' => 1, 'applicable_to' => 'medical', 'has_expiry' => 1, 'sort_order' => 12],
            ['name' => 'Sertifikat Kompetensi', 'category' => 'Sertifikasi', 'is_mandatory' => 1, 'applicable_to' => 'medical', 'has_expiry' => 1, 'sort_order' => 13],
            
            // === DOKTER ONLY ===
            ['name' => 'STR Dokter', 'category' => 'Sertifikasi', 'is_mandatory' => 1, 'applicable_to' => 'doctor', 'has_expiry' => 1, 'sort_order' => 14],
            ['name' => 'SIP Dokter', 'category' => 'Sertifikasi', 'is_mandatory' => 1, 'applicable_to' => 'doctor', 'has_expiry' => 1, 'sort_order' => 15],
            ['name' => 'Sertifikat Spesialis', 'category' => 'Sertifikasi', 'is_mandatory' => 1, 'applicable_to' => 'doctor', 'has_expiry' => 0, 'sort_order' => 16],
            
            // === OPTIONAL (Supporting Documents Examples) ===
            ['name' => 'Sertifikat Pelatihan', 'category' => 'Pelatihan', 'is_mandatory' => 0, 'applicable_to' => 'all', 'has_expiry' => 1, 'sort_order' => 100],
            ['name' => 'SK Pengangkatan', 'category' => 'Kepegawaian', 'is_mandatory' => 0, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 101],
            ['name' => 'Kontrak Kerja', 'category' => 'Kepegawaian', 'is_mandatory' => 0, 'applicable_to' => 'all', 'has_expiry' => 1, 'sort_order' => 102],
            ['name' => 'Sertifikat Penghargaan', 'category' => 'Penghargaan', 'is_mandatory' => 0, 'applicable_to' => 'all', 'has_expiry' => 0, 'sort_order' => 103],
        ];

        foreach ($mandatory_docs as $doc) {
            $doc['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('document_types', $doc);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column('document_types', 'is_mandatory');
        $this->dbforge->drop_column('document_types', 'applicable_to');
        $this->dbforge->drop_column('document_types', 'sort_order');
        $this->dbforge->drop_column('employee_documents', 'is_supporting');
    }
}
