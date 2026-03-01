<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_User_Status_Approval extends CI_Migration {

    public function up()
    {
        // Add "pending" and "rejected" to status enum in users table
        // Since sqlite/mysql handle this differently, we'll use a direct query for MySQL
        $this->db->query("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'pending', 'rejected') DEFAULT 'pending'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
}
