<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->library(array('twconnect'));
	}
	public function index()
	{
		/*if($this->session->userdata('login') == true){
			redirect('welcome/profile');
		}*/
		
		$this->load->view('welcome_message');
	}
	
	public function redirect() {

		/*if($this->session->userdata('login') == true){
			redirect('welcome/profile');
		}*/
		
		$ok = $this->twconnect->twredirect('welcome/callback');

		if (!$ok) {
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
		
	}


	public function callback() {
		
		/*if($this->session->userdata('login') == true){
			redirect('welcome/profile');
		}*/
		
		$ok = $this->twconnect->twprocess_callback();
		
		
		if ( $ok ) { //redirect('http://localhost/scorpion/www/member/test.html');
		//redirect('http://localhost/scorpion/www/member/social_connect.html'); }
		//	else redirect ('welcome/failure');
			echo json_encode($ok);
			redirect('http://localhost/scorpion/www/index.html#');
	//pass_url('member/social_connect.html');
}}


	public function success() {

		/*if($this->session->userdata('login') == true){
			redirect('welcome/profile');
			redirect('http://localhost/scorpion/www/member/test.html');
		}*/
		
		$this->twconnect->twaccount_verify_credentials();

		
		$user_profile = $this->twconnect->tw_user_info;
		
		$this->session->set_userdata('login',true);
		

		$arr = array(
			'id_str' => $user_profile->id_str,
			'name' => $user_profile->name,
			'screen_name' => $user_profile->screen_name,
			'location' => $user_profile->location,
			'description' => $user_profile->description,
			'profile_image_url' => $user_profile->profile_image_url,
		);
		echo json_encode($arr);
		$this->session->set_userdata('user_profile',$arr);
		
		//redirect('welcome/profile');
		
	}



	public function failure() {

		/*if($this->session->userdata('login') == true){
			redirect('welcome/profile');
		}*/
		
		echo '<p>Twitter connect failed</p>';
		echo '<p><a href="' . base_url() . 'welcome/logout">Try again!</a></p>';
	}
	
	public function profile(){
		if($this->session->userdata('login') != true){
			redirect('');
		}
		$contents['user_profile'] = $this->session->userdata('user_profile');
		$this->load->view('profile',$contents);
		
	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect('');
		
	}

	public function checkSession(){
		if($this->session->userdata('login') == true){
			$twtsession = $this->session->userdata('login');
			echo json_encode($twtsession);
		}
	}
}
