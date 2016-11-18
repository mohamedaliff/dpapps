<?php

class Apps_security extends CI_Controller {

        public function __construct()
        {
                parent::__construct();
                $this->load->helper(array('form', 'url'));
                $this->load->model('account_db');
                $this->load->database();
                $this->load->library('session');                
        }

        public function checkPinCode() { //retreive query result from getPinCode()

        $sessionName = $this->session->userdata('username'); //get value in session
        $this->load->model('account_db');
        $data = $this->account_db->getPinCode($sessionName);
        echo json_encode($data);       
        }

        
}