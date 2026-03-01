<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['settings'] = $this->db->get('settings')->row();
        $data['smtp'] = $this->db->get('email_settings')->row();
        
        $this->load->view('layout/header');
        $this->load->view('settings/index', $data);
        $this->load->view('layout/footer');
    }

    public function update()
    {
        $data = [
            'company_name' => $this->input->post('company_name'),
            'address'      => $this->input->post('address'),
            'contact'      => $this->input->post('contact'),
            'email'        => $this->input->post('email'),
            'latitude'     => $this->input->post('latitude'),
            'longitude'    => $this->input->post('longitude'),
            'radius_meters'=> $this->input->post('radius_meters'),
            'hr_signature_name' => $this->input->post('hr_signature_name'),
            'hr_signature_title'=> $this->input->post('hr_signature_title'),
            'header_color' => $this->input->post('header_color'),
            'sidebar_color'=> $this->input->post('sidebar_color'),
            'footer_color' => $this->input->post('footer_color'),
            'menu_active_color' => $this->input->post('menu_active_color'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        // Update SMTP
        $smtp_data = [
            'protocol'  => $this->input->post('protocol'),
            'smtp_host' => $this->input->post('smtp_host'),
            'smtp_user' => $this->input->post('smtp_user'),
            'smtp_pass' => $this->input->post('smtp_pass'),
            'smtp_port' => $this->input->post('smtp_port'),
            'smtp_crypto'=> $this->input->post('smtp_crypto')
        ];
        $this->db->where('id', 1)->update('email_settings', $smtp_data);

        // Handle Logo Upload
        if (!empty($_FILES['company_logo']['name'])) {
            $config['upload_path']   = './uploads/company/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = 'logo_' . time();
            $config['overwrite']     = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('company_logo')) {
                $upload_data = $this->upload->data();
                $data['company_logo'] = 'uploads/company/' . $upload_data['file_name'];
            }
        }

        $this->db->where('id', 1)->update('settings', $data);
        $this->session->set_flashdata('success', 'Pengaturan berhasil diperbarui!');
        redirect('settings');
    }
}
