<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_payroll_system extends CI_Migration {

    public function up()
    {
        // 1. Payroll Periods Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'period_month' => ['type' => 'INT', 'constraint' => 2],
            'period_year' => ['type' => 'INT', 'constraint' => 4],
            'status' => ['type' => 'ENUM("draft","approved","paid")', 'default' => 'draft'],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('payroll_periods');

        // 2. Employee Salary Settings Table (Stored on Employees or separate table? Let's add to employees)
        $fields = [
            'basic_salary' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'bank_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'bank_account' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'bank_account_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
        ];
        $this->dbforge->add_column('employees', $fields);

        // 3. Payroll Slips Table (The actual salary slip per employee per period)
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'period_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'employee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'basic_salary' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_allowance' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_deduction' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'net_salary' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'attendance_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'late_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'absent_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status' => ['type' => 'ENUM("draft","paid")', 'default' => 'draft'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('payroll_slips');

        // 4. Payroll Slip Details (Specific items like Transport allowance, Late deduction, etc)
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'slip_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'type' => ['type' => 'ENUM("allowance","deduction")'],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('payroll_slip_details');
    }

    public function down()
    {
        $this->dbforge->drop_table('payroll_slip_details', TRUE);
        $this->dbforge->drop_table('payroll_slips', TRUE);
        $this->dbforge->drop_table('payroll_periods', TRUE);
        $this->dbforge->drop_column('employees', 'basic_salary');
        $this->dbforge->drop_column('employees', 'bank_name');
        $this->dbforge->drop_column('employees', 'bank_account');
        $this->dbforge->drop_column('employees', 'bank_account_name');
    }
}
