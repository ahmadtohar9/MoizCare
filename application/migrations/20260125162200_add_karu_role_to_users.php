<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Karu_Role_To_Users extends CI_Migration {

    public function up()
    {
        // Update the users table role column to include 'karu'
        // Using direct SQL since dbforge modify_column can be tricky with ENUMs across different DB versions
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'user', 'karu') NOT NULL DEFAULT 'user'");
    }

    public function down()
    {
        // Revert back if needed, but risky if 'karu' records exist
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }
}
