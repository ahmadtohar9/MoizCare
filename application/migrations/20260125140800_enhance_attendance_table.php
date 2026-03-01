<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Enhance_Attendance_Table extends CI_Migration {

    public function up()
    {
        $fields = array(
            'photo_in' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE),
            'photo_out' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE),
            'location_lat_in' => array('type' => 'DECIMAL', 'constraint' => '10,8', 'null' => TRUE),
            'location_long_in' => array('type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE),
            'location_lat_out' => array('type' => 'DECIMAL', 'constraint' => '10,8', 'null' => TRUE),
            'location_long_out' => array('type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE),
            'distance_meters_in' => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
            'distance_meters_out' => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
            'shift_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
        );
        $this->dbforge->add_column('attendance', $fields);
        
        // Add foreign key for shift_id
        $this->db->query('ALTER TABLE attendance ADD CONSTRAINT fk_attendance_shift FOREIGN KEY (shift_id) REFERENCES master_shifts(id) ON DELETE SET NULL');
    }

    public function down()
    {
        // For sqlite/mysql safety, we just drop columns
        $this->dbforge->drop_column('attendance', 'photo_in');
        $this->dbforge->drop_column('attendance', 'photo_out');
        $this->dbforge->drop_column('attendance', 'location_lat_in');
        $this->dbforge->drop_column('attendance', 'location_long_in');
        $this->dbforge->drop_column('attendance', 'location_lat_out');
        $this->dbforge->drop_column('attendance', 'location_long_out');
        $this->dbforge->drop_column('attendance', 'distance_meters_in');
        $this->dbforge->drop_column('attendance', 'distance_meters_out');
        $this->dbforge->drop_column('attendance', 'shift_id');
    }
}
