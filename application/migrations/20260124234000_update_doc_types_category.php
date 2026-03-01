<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_Doc_Types_Category extends CI_Migration {

    public function up()
    {
        $fields = array(
            'category' => array(
                'type' => 'ENUM("all", "medical", "non-medical")',
                'default' => 'all',
                'after' => 'is_required'
            ),
        );
        $this->dbforge->add_column('master_document_types', $fields);

        // Update Existing Data Defaults
        $this->db->update('master_document_types', ['category' => 'medical'], "name LIKE '%STR%' OR name LIKE '%SIP%'");
    }

    public function down()
    {
        $this->dbforge->drop_column('master_document_types', 'category');
    }
}
