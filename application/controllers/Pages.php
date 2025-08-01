<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
date_default_timezone_set('Asia/Manila');
    class Pages extends CI_Controller{
        public function index(){
            $page = "index";
            if(!file_exists(APPPATH.'views/pages/'.$page.".php")){
                show_404();
            }                                
            $userdata=array('firstname','lastname','email','contactno','password','ctime');
            $this->session->unset_userdata($userdata);
            if($this->session->admin_login){redirect(base_url('main'));}            
            else{}
            $this->load->view('pages/'.$page);                 
        }
        public function register(){
            $page = "register";
            if(!file_exists(APPPATH.'views/pages/'.$page.".php")){
                show_404();
            }               
            $userdata=array('firstname','lastname','email','contactno','password','ctime');
            $this->session->unset_userdata($userdata);                      
            if($this->session->user_login){redirect(base_url('main'));}
            else{}
            $this->load->view('pages/'.$page);                 
        }
        public function submit_registration(){
            $firstname=$this->input->post('app_firstname');
            $lastname=$this->input->post('app_lastname');
            $contactno=$this->input->post('app_contactno');
            $email=$this->input->post('app_email');
            $password=$this->input->post('app_password');
            $exist=$this->Application_model->checkEmailExist($email);
            if($exist){
                $this->session->set_flashdata('feedback','Email already exist!');
                redirect(base_url('register'));
            }else{                                
                    $code=substr(str_shuffle("0123456789"),0,4);                    
                    if($this->Application_model->send_SMS($contactno,$code,$firstname)){
                        $this->Application_model->register_user();
                        $userdata=array(
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                            'contactno' => $contactno,
                            'email' => $email,
                            'password' => $password,
                        );
                        $this->session->set_userdata($userdata);
                        $this->Application_model->save_verification($email,$code);
                        redirect(base_url('verification'));
                    }else{
                        redirect(base_url('error_page'));
                    } 
             }
           
        }
        public function error_page(){
            $page = "error_page";
            if(!file_exists(APPPATH.'views/pages/'.$page.".php")){
                show_404();
            }                                     
            if($this->session->user_login){redirect(base_url('main'));}
            else{}
            $this->load->view('pages/'.$page);                 
        }
        public function verification(){
            $page = "authentication";
            if(!file_exists(APPPATH.'views/pages/'.$page.".php")){
                show_404();
            }                                     
            if($this->session->user_login){redirect(base_url('main'));}
            else{}
            $this->load->view('pages/'.$page);                 
        }
        public function resend_code(){
             $firstname=$this->session->firstname;
             $contactno=$this->session->contactno;
             $email=$this->session->email;
             $code=substr(str_shuffle("0123456789"),0,4);
             if($this->Application_model->send_SMS($contactno,$code,$firstname)){                
                $this->Application_model->save_verification($email,$code);
                $this->session->set_userdata('ctime','1');
                redirect(base_url('verification'));
            }else{
                redirect(base_url('error_page'));
            }
        }
        public function verify_account(){
            $c1=$this->input->post('c1');
            $c2=$this->input->post('c2');
            $c3=$this->input->post('c3');
            $c4=$this->input->post('c4');
            $code=$c1."".$c2."".$c3."".$c4;
            $verify=$this->Application_model->verify_account($code);
            if($verify){                
                redirect(base_url('success_verify'));
            }else{
                $this->session->set_flashdata('error','Invalid one-time code!');
                redirect(base_url('verification'));
            }            
        }
        public function success_verify(){
            $page = "success_verify";
            if(!file_exists(APPPATH.'views/pages/'.$page.".php")){
                show_404();
            }                                     
            if($this->session->user_login){redirect(base_url('main'));}
            else{}
            $this->load->view('pages/'.$page);                 
        }
}
?>
