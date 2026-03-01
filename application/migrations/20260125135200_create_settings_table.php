<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Settings_Table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'company_name' => array('type' => 'VARCHAR', 'constraint' => '255'),
            'address' => array('type' => 'TEXT', 'null' => TRUE),
            'contact' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
            'email' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
            'latitude' => array('type' => 'DECIMAL', 'constraint' => '10,8', 'null' => TRUE),
            'longitude' => array('type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE),
            'radius_meters' => array('type' => 'INT', 'constraint' => 11, 'default' => 100), // Default 100 meters
            'updated_at' => array('type' => 'DATETIME', 'null' => TRUE),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('settings');

        // Seed initial settings
        $data = [
            'company_name' => 'Moiz Care Hospital',
            'address' => 'Jl. Kesehatan No. 123, Jakarta',
            'contact' => '021-12345678',
            'email' => 'info@moizcare.com',
            'latitude' => -6.2088, // Example Jakarta lat
            'longitude' => 106.8456, // Example Jakarta long
            'radius_meters' => 100,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('settings', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('settings');
    }
}
