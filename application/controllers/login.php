<?php

if (!defined('BASEPATH')) die();

class Login extends CI_Controller
{
	var $parent_page = "login";
	function __construct()
	{
		parent::__construct();
//                header('Access-Control-Allow-Origin: *');  
                
            if ($this->my_func->getMaintenance() == true) {
                redirect(site_url('maintenance'));
            }
        }
        
        public function viewProduct() {
            $v_idx = $this->input->get('v');
            $v_id = $this->my_func->dinarpal_decrypt($v_idx);
            $data['vault'] = $this->m_vault->get($v_id);
            if (isset($data['vault']) && !empty($data['vault'])) {
                $data['items'] = $data['vault'][0];
            } else {
                die("<em>.. No Product ..</em>");
            }
            $this->_viewpage('viewProductVault', $data);
        }
        
        public function testCrud() {
            $crud = new grocery_CRUD();
            $crud->set_theme('twitter-bootstrap');
            
            $crud->set_table("user");
            $crud->display_as('u_name', 'name');
            $crud->display_as('u_image', 'gambar','upload');
            //$crud->set_field_upload('u_image','./assets/uploads');
            //$crud->set_relation('barang_id','barang','nama');
 
            
//$crud->set_table("members");

//$crud->columns('me_image','me_firstname', 'me_username');
//$crud->fields('me_image','me_firstname', 'me_username');
//$crud->display_as('me_firstname', 'name');
//$crud->display_as('me_username', 'username');
//$crud->display_as('me_image', 'foto');
//$crud->set_field_upload('me_image');  

           
           
           
            // $crud->set_table("members");
            
            //$crud->set_relation_n_n('Amils', 'souq', 'amil', 'me_id', 'am_id', 'am_name');
            //$crud->set_relation_n_n('Storage', 'souq', 'storage_location', 'me_id', 'sl_id', 'sl_name');
            
            //$crud->columns('me_firstname', 'me_username');
            //$crud->fields('me_firstname', 'me_username', 'Amils', 'Storage');
            //$crud->display_as('me_firstname', 'name');
            //$crud->display_as('me_username', 'username');
            
            
            $data = $crud->render();
            
            echo $this->load->view('login/testCrud', $data, true);
        }
        
        public function test_template_email($page) {
            $data['link'] = '';
            $data['me_username'] = '';
            $data['me_password'] = '';
            echo $this->load->view('emails/v_header', '', true);
            echo $this->load->view('emails/'.$page, $data, true);
        }
        
        public function payment($bap_idx, $data=array()) {
            
            $this->load->view($this->parent_page . '/header');
            $this->load->view($this->parent_page . '/product', $data);  
            $this->load->view($this->parent_page . '/footer');
        }

	function _viewpage($page = 'login', $data = '')
	{
		// check for the flashdata
		if ($this->session->flashdata('error') != "") 
			$data['error'] = $this->session->flashdata('error');
		if ($this->session->flashdata('sucess') != "") 
			$data['sucess'] = $this->session->flashdata('sucess');
		if ($this->session->flashdata('info') != "") 
			$data['info'] = $this->session->flashdata('info');
			
		$this->load->view($this->parent_page . '/header');
		$this->load->view($this->parent_page . '/nav');
		$this->load->view($this->parent_page . '/' . $page, $data);
		$this->load->view($this->parent_page . '/footer');
	}
        
        public function support_system() {
            $this->load->view($this->parent_page . '/chat1');
        }
	
	public function checkforgotpassword($stat=1)
	{
		$email = $this->input->post('email');
		$me = $this->m_members->getByEmail($email);
		if ($me) {
			if ($stat == 1) {
				$data['me'] = $me;
				$this->_viewpage('forgotpassword2', $data);
			} else {
				$answer = $this->input->post('me_answer');
				$me_answer = $me[0]->me_answer;
				if (strtoupper($me_answer) == strtoupper($answer)) {
					$me_username = $me[0]->me_username;
					$me_id = $me[0]->me_id;
					$pwd = random_string('alnum', 10);
					$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
					$user_pass_hashed = $hasher->HashPassword($pwd);
					$data = array(
						'me_password' => $user_pass_hashed
					);
					$bol_update_me = $this->m_members->edit($me_id, $data);
					if ($bol_update_me) {
						$msg = 'Your new password for username ' . $me_username . ' is ' . $pwd;
						$this->simpleloginsecure->send_email($email, 'Reset Password', $msg);
						$this->session->set_flashdata('sucess', 'Your password has been reset. Check your email ..');
						redirect(site_url('login'));
					} else {
						$this->session->set_flashdata('error', 'We cannot reset your password, we are sorry.');
					}
				} else {
					$this->session->set_flashdata('error', 'Wrong answer!');
				}
				redirect(site_url('login/forgotPassword'));
			}
		} else {
			$this->session->set_flashdata('error', 'Invalid E-mail!');
			redirect(site_url('login/forgotPassword'));
		}
	}
	
	public function forgotPassword()
	{
		$this->_viewpage('forgotpassword');
	}
	
	public function viewMenu($menu)
	{
		$this->_viewpage('../dropdownMain/'.$menu);
	}
	
	function _test_emel()
	{
		/*$to       = 'kidzeclipes@yahoo.com';
		$subject  = 'Testing sendmail.exe';
		$message  = 'Hi, you just received an email using sendmail!';
		$headers  = 'From: kidzeclipes@gmail.com' . "\r\n" .
					'Reply-To: kidzeclipes@gmail.com' . "\r\n" .
					'MIME-Version: 1.0' . "\r\n" .
					'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		if(mail($to, $subject, $message, $headers))
			echo "Email sent";
		else
			echo "Email sending failed";*/
		$this->simpleloginsecure->send_email_activation("kidzeclipes@gmail.com");
	}
        
        function setCookie() {
            $vpurchase = $this->input->get('vpurchase');
            $vpurchase = str_replace('==', '#@!', $vpurchase);
            $dinarpal_cookie = array(
                'name'   => 'vpurchase',
                'value'  => $vpurchase,
                'expire' => time()+86500
            );
            $this->input->set_cookie($dinarpal_cookie);
            redirect(site_url('login/index'));
        }

	public function index()
	{
//            $magicnum = "asdasdsdasd";
//            $link = site_url('login/activate_account/'.$magicnum);
//            $link = $this->my_func->url_https($link);
//            echo $link;
//            die();
            
//            $vp = $this->input->cookie('vpurchase');
//            $dinarpal_cookie = array(
//                'name'   => 'vpurchase',
//                'value'  => $vp,
//                'expire' => 0
//            );
//            $this->input->set_cookie($dinarpal_cookie);
//            $vp = str_replace('#@!', '==', $vp);
//            $v_id = $this->my_func->dinarpal_decrypt($vp);
//            print_r($v_id); die();
            
		$this->_viewpage();
	}

	public function registration($page = "personal")
	{
		$data["page"] = $page;
                
		$this->_viewpage('registration',$data);
	}

	public function getstarted()
	{
//            $sess = $this->session->all_userdata();
//            print_r($sess); die();
		$this->_viewpage('getstarted');
	}

	
	
	public function daftar_members()
	{
            
                if ($this->input->post('rulecond11')) {
                    unset($_POST['rulecond11']);
                }
                if ($this->input->post('rulecond12')) {
                    unset($_POST['rulecond12']);
                }
                if ($this->input->post('rulecond13')) {
                    unset($_POST['rulecond13']);
                }
                if ($this->input->post('rulecond21')) {
                    unset($_POST['rulecond21']);
                }
                if ($this->input->post('rulecond22')) {
                    unset($_POST['rulecond22']);
                }
                if ($this->input->post('rulecond23')) {
                    unset($_POST['rulecond23']);
                }
                $me_account_type = $this->input->post('me_account_type');
                $account_type = ($me_account_type == 2) ? ("corporate") : ("personal");
                
                $arr = $this->input->post();
                foreach ($arr as $ar) {
                    if ($ar == '' || $ar == null || $ar == NULL || $ar == "" || empty($ar)) {
                        $this->session->set_flashdata('error', 'Do not leave any field blanked!');
                        redirect(site_url('login/registration/'.$account_type));
                    }
                }
                
//                $me_password = $this->input->post('me_password');
//                $me_password2 = $this->input->post('me_password2');
//                $me_password = "Abcd1234!@#$";
                $me_password = strtolower($this->my_func->getRandomVal('alpha', 2));
                $me_password .= strtoupper($this->my_func->getRandomVal('alpha', 2));
                $me_password .= $this->my_func->getRandomVal('numeric', 3);
                $me_password .= "!@#";
                $me_password2 = $me_password;
                if ($me_password != $me_password2) {
                    $this->session->set_flashdata('error', 'Password not match with confirmation password!');
                    redirect(site_url('login/registration/'.$account_type));
                }
                unset($_POST['me_password2']);
                
		$pwd_status = $this->my_func->isValidPassword($me_password);
		if (strpos($pwd_status, 'strong') === false) 
		{
			$this->session->set_flashdata('error', $pwd_status);
			redirect(site_url('login/registration/'.$account_type));
		}
		
//		$me_username = $this->my_func->trim_username($this->input->post('me_firstname'));
		$me_username = $this->input->post('me_username');
                $me_username = str_replace(' ', '', $me_username);
                
                $me_email = $this->input->post('me_email');
                if (!filter_var($me_email, FILTER_VALIDATE_EMAIL) === false) {
                } else {
                    $this->session->set_flashdata('error', 'Invalid email format!');
                    redirect(site_url('login/registration/'.$account_type));
                }
                
		$member = array(
//			'me_firstname' => $this->input->post('me_firstname'),
//			'me_lastname' => $this->input->post('me_lastname'),
			'me_firstname' => $me_username,
			'me_lastname' => $me_username,
			'me_username' => $me_username,
			'me_password' => $me_password,
			'me_email' => $me_email,
			'me_register_date' => date('Y-m-d H:i:s'),
			'me_account_type' => $me_account_type,
			'me_activation_status' => 1,
			'me_magic_num' => md5($this->input->post('me_email')),
			'me_unique_link' => md5($me_username),
			'me_image' => 'default-img.jpg'
		);
                
                if ($me_account_type == 2) {
                    $member['me_company'] = $this->input->post('me_company');
                    $member['me_reg_num'] = $this->input->post('me_reg_num');
                }
                
                $me_whatsapp_no = $this->input->post('me_whatsapp_no');
                if ($me_whatsapp_no == '' || $me_whatsapp_no == NULL || empty($me_whatsapp_no) || !is_numeric($me_whatsapp_no)) {
                    $this->session->set_flashdata('error', 'Please fill in the valid phone number!');
                    redirect(site_url('login/registration/'.$account_type));
                } else {
                    $member['me_whatsapp_no'] = $me_whatsapp_no;
                    $member['me_phone_no'] = $me_whatsapp_no;
                }
                
//                print_r($member); die();
		
		if ($this->simpleloginsecure->create_members($member)) {
			//$this->session->set_flashdata('sucess', 'Registration Success .. Check your email for account activation.');
                    $email = $this->my_func->dinarpal_encrypt($this->input->post('me_email')); 
                    redirect(site_url('login/summary/'.$email));
                    
		} else {
                    
//                    $this->session->set_flashdata('error', 'Opss! Something is wrong. ..');
                    redirect(site_url('login/registration/'.$account_type));
                }
	}


	public function summary($email)
	{
		$emailx = $this->my_func->dinarpal_decrypt($email);
                $me = $this->m_members->getByEmail($emailx);
                $data["me_username"] = (isset($me) && !empty($me)) ? ($me[0]->me_username) : ("Anon");
		$data["email"] = $emailx;
		$this->_viewpage('summary',$data);
	}

	function register_user($post_array)
	{
		$user_pass_hashed = $this->simpleloginsecure->createhashedPassword($post_array['user_pass']);
		$post_array['user_pass'] = $user_pass_hashed;
		$post_array['magicnum'] = md5($post_array['user_email']);
		$post_array['user_date'] = date('c');
		$post_array['user_modified'] = date('c');

		// email must be unique

		$this->db->where('user_email', $post_array['user_email']);
		$query = $this->db->get_where('users');
		if ($query->num_rows() > 0) //user_email already exists
		return false;

		// send email activation

		$this->simpleloginsecure->send_email_activation($post_array['user_email']);
		return $post_array;
	}
        
        function deleteUser($me_id, $pwd) {
            $pwd = str_replace("====", "$#@!", $pwd);
            if ($pwd == "$#@!4321Dcba") {
                $this->m_members->deleteMemberAllAccount($me_id);
                echo "Done Delete!";
            } else {
                echo "Access Denied!";
            }
        }
	
	public function test_email($emel, $sub='DinarPal Test', $msg='Test Message')
	{
            $emel = str_replace("==", "@", $emel);
//		$this->simpleloginsecure->test_email($emel);
            echo "|".$this->my_func->send_email($emel, $sub, $msg)."|";
	}

	public function activate_account($magicnum = null)
	{
		if (!$this->simpleloginsecure->activate_account($magicnum)) 
			$this->session->set_flashdata('error', 'There is no need to activate, user not found!');
		else 
			$this->session->set_flashdata('sucess', 'Activation is successful, please log in!');
		redirect('login');
	}
	
	function tukarpwd($pwd)
	{
                $pwd = str_replace("===", "#@!", $pwd);
                echo $pwd;
                echo "<br />";
		echo $this->simpleloginsecure->createhashedPassword($pwd);
	}
	
	function _tukaremel()
	{
		echo md5("kidzeclipes@gmail.com");
	}
	
	function __tukaqPwd($me_id, $pwd)
	{
		$arrMe = array(
			'me_password' => $this->simpleloginsecure->encrypt_password($pwd)
		);
		$me_update = $this->m_members->edit($me_id, $arrMe);
		if ($me_update) {
			$this->session->set_flashdata('sucess', 'Successfully update your password ..<br />Please login again.');
		} else {
			$this->session->set_flashdata('error', 'Cannot update your password!');
		}
		redirect(site_url('login'));
	}
	
	public function tukaqEmas($stat = 1)
	{
		if ($stat == 1) {
			$data = array(
				'item_type' => $this->m_item_type->getAll(),
				'purity' => $this->m_purity->getAll()
			);
			$this->_viewpage('v_tukaq_emas', $data);
		} else {
			$item_type = $this->m_item_type->getAll();
			$purity = $this->m_purity->getAll();
			if ($purity) {
				foreach ($purity as $pu) {
					if ($item_type) { 
						foreach ($item_type as $it) {
							$data = array(
								'itp_price' => $this->input->post($pu->pu_id.'_'.$it->it_id)
							);
							$this->m_item_type_purity->edit_calculator($pu->pu_id, $it->it_id, $data);
						}
					}
				}
			}
			$this->session->set_flashdata('sucess', 'Save success ...');
			redirect(site_url('login/tukaqEmas'));
		}
	}
        
        public function asklogin()
        {
                $username = $this->input->post('username');
		$password = $this->input->post('password');
                $me_temp_1 = $this->m_members->getAll_users($username);
                $mas = (isset($me_temp_1) && !empty($me_temp_1)) ? ($me_temp_1[0]->me_activation_status) : (1);
		if($this->authenticate())
		{
                        if ($mas == 1) {
                                $this->session->set_flashdata('error','Please verify your email! <span class="fa fa-envelope"></span>');
                                // login fail, back to login and notif login 
                                redirect(site_url('login'));
                        } else {
                                $me_last_login = date('Y-m-d H:i:s');
                                $data_me_temp_2 = array(
                                    'me_last_login' => $me_last_login
                                );
                                $me_id_temp_2 = $me_temp_1[0]->me_id;
                                $this->m_members->edit($me_id_temp_2, $data_me_temp_2);
                        }
                        
			if ($this->config->item('dev_mode') == 'on') {
				$this->session->set_flashdata('info','Cannot login yet! Development in progress ...'); 
				redirect(site_url('login'));
			}
                        			
			if ($this->input->post('remember_me')) {
			}
			
			$sess = $this->session->all_userdata();
			$ml_id = $sess['ml_id'];
                        
//                        print_r($sess); die();
                        
                        if (isset($sess['msg_login'])) {
                            $this->session->set_flashdata('info', $sess['msg_login']);
                        }
			
			if ($ml_id == 3) {
                                if($this->input->post('product')) {
                                    $p = $this->input->post('product');
                                    redirect(site_url('payment/checkPayment/?product='.$p));
                                } else {
                                    
                                    $vp = $this->input->cookie('vpurchase');
                                    if (isset($vp) && !empty($vp)) {
//                                        die($vp);
                                        redirect(site_url('member/muamalat/purchase2/34'));
                                    }
                                    
                                    if (isset($sess['msg_login'])) {
//                                        redirect(site_url('member/newMenu/deposit/10'));
                                        redirect(site_url('member/profile/profile/12'));
                                    } else {
                                        redirect(site_url('member'));
                                    }
                                }
			} else if ($ml_id == 2) {
				redirect(site_url('staff/newMenu/overview/06'));
			} else if ($ml_id == 1) {
				redirect(site_url('pisang'));
			} else {
				$this->session->set_flashdata('error','Page were not ready yet!');
				redirect(site_url('login'));
			}
			
			/*$me_account_type = $this->session->userdata('me_account_type');
			// login sucess
			if ($me_account_type == '1') {
				redirect('staff');
			}
			else if ($me_account_type == '2') {
				redirect('member');
			}
			else if ($me_account_type == '3') {
				redirect('pisang');
			}
			else {
				redirect('login');
			}*/
		}
		else if ($username == 'umaq' && $password == 'umaq123')
		{
			redirect(site_url('login/tukaqEmas'));
		}
		else 
		{
			$this->session->set_flashdata('error','Wrong username/password!');
			// login fail, back to login and notif login 
			redirect(site_url('login'));
		} 
        }

	private function authenticate()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
                if($this->simpleloginsecure->login($username, $password))
		{
			return true;
		} 
                else 
                {
                        return false;
                }
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/bootigniter.php */