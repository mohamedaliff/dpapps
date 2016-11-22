<?php

class Social_connect extends CI_Controller {

        public function __construct()
        {
                parent::__construct();
                $this->load->helper(array('form', 'url'));
                $this->load->model('account_db');
                $this->load->database();
                $this->load->library('session');                
        }


        public function getPin() {
                
                $this->load->model('account_db');
                $getPin  = $this->account_db->add_pin();
        
                if(!empty($getPin)){
                        return true;
                }else{
                        return false;
                }
        } 

        
}