<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();	
	}
	
	
	
	public function index(){		
		
	 }
        #--------------------------------------------------------------------
	# function for Login Authentication
	#---------------------------------------------------------------------		
	  public function login(){
		 $postdata=$this->input->post();
		 //$postdata = json_decode(file_get_contents("php://input"),true);
		 $this->load->model('Register_model');
		 $email=$postdata['email'];
		 $password=$postdata['password'];
		 $result=$this->Register_model->check_login($email,$password);
	    	 if($result[0]['status']==2)
		 {
			$uid=$result[0]['user_id'];
			$apikey=$password.$result[0]['email'].time();
			$key=md5($apikey);
			$characters =$key;
			$randomString = '';
			for ($i=0;$i<10;$i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			$res=$this->Register_model->updateKey($uid,$randomString);	    
			if($res==1){
				$response=array('status'=>'1', 'message'=>'Login successfully','userId'=>$result[0]['user_id'],'userkey'=>$randomString);  

			}
			else{
				$response=array('status'=>'0', 'message'=>'Please login again','userId'=>$result[0]['user_id']);
			} 
		 }
		 else
		 {
			  $response=array('status'=>'3', 'message'=>'Invalid username or password');
			  
		 }
		echo json_encode($response);exit;	 
	 }
        #--------------------------------------------------------------------
	# function for general signup
	#---------------------------------------------------------------------	
	public function signUp() {
		$data=$this->input->post();
		$this->load->model('Register_model');
		//$data = json_decode(file_get_contents("php://input"),true);
		if(($data['name'] != "") && ($data['email'] != "") && ($data['mobilenumber'] != "") && ($data['password'] != "") && ($data['gender'] != "") && ($data['dob'] != ""))
		{
		$name=$data['name'];
		$mobileNumber=$data['mobilenumber'];
		$email= $data['email'];
		$password=$data['password'];
		$pwd=md5($password);
		$gender=$data['gender'];
		$dob=$data['dob'];
		$vcode=substr(uniqid('', true), -5);
		$varification_code = $this->checkverficationnumberfuntion($vcode);
		$today = date("Y-m-d H:i:s");
		$status = '2';
		$ip_addr = $this->input->ip_address();
		/*@$txtReferralCode=$data['refcode'];
		if($this->Register_model->check_user($email)){
		$referred_by = 0;
		if(!empty($txtReferralCode)){
		$referred_by =$this->Register_model->getUidByCode($txtReferralCode);
		if($referred_by == 0){ 
		$response=array('status'=>'2', 'reg'=>'Enter valid referal code');
		echo json_encode($response);}
		}*/
		$userid =$this->Register_model->addUser($name,$email,$mobileNumber,$pwd,$gender,$dob,$varification_code, $today,$status,$ip_addr);
			if(intval($userid) >0){			
			//$sms_message =$this->Register_model->RegSMSMSG($varification_code);
			//$this->Register_model->sendSMS($mobileNumber,$sms_message);
			$response=array('status'=>'1', 'reg'=>'Register Successfully');
			}			  
			else{ 
			$response=array('status'=>'0', 'reg'=>'Registration fail please try again');
			}	 
		}
		else
		{
		 $response=array('status'=>'0', 'reg'=>'Please fill all the details');
		}
		echo json_encode($response);

	}
	#--------------------------------------------------------------------
	# function for checking the verification code
	#---------------------------------------------------------------------	
	function checkverficationnumberfuntion($vcode){
		$this->load->model('Register_model');
		$checkvrnumber = $this->Register_model->checkverCode($vcode);
		if($checkvrnumber==1){
			$vcode=substr(uniqid('', true), -5);
			$this->checksrnumberfuntion($vcode);
		}else{
			$vrnumbercode =$vcode;
		} 
		return $vrnumbercode;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
