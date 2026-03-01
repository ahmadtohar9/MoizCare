<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_payroll_components extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'type' => ['type' => 'ENUM("allowance","deduction")'],
            'calculation_basis' => ['type' => 'ENUM("fixed_monthly","per_attendance","per_late_day")', 'default' => 'fixed_monthly'],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('master_payroll_components');

        // Insert default data mimicking the previous hardcoded behavior
        $data = [
            [
                'name' => 'Tunjangan Transportasi',
                'type' => 'allowance',
                'calculation_basis' => 'per_attendance',
                'amount' => 20000,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Tunjangan Uang Makan',
                'type' => 'allowance',
                'calculation_basis' => 'per_attendance',
                'amount' => 30000,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Potongan Keterlambatan',
                'type' => 'deduction',
                'calculation_basis' => 'per_late_day',
                'amount' => 50000,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->insert_batch('master_payroll_components', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('master_payroll_components');
    }
}
