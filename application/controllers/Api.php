<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'libraries/REST_Controller.php';
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
 
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }
class Api extends REST_Controller {
	public function __construct()
	{
	  parent :: __construct();
	  $this->load->helper('my_api');
	  $this->load->model('Common_model');
	  
	}
	#--------------------------------------------------------------------
	# function for change password
	#---------------------------------------------------------------------	
	public function changepassword_Post()
	{
		$data=$this->input->post();
		$this->load->model('Register_model');
		//$data = json_decode(file_get_contents("php://input"),true);
		$password=$data['password'];
		$userid=$data['user_id'];
		$result=$this->Register_model->changepassword($password,$userid);
		if($result==1)
		{
			$this->response(array('status'=>'1','message'=>'Password updated succesfully',
			REST_Controller::HTTP_NOT_FOUND));
		}
		$this->response(array( 'status'=>'0','message'=>' your have set the previously used password',
		REST_Controller::HTTP_NOT_FOUND));
	}
	#--------------------------------------------------------------------
	# function for feedback
	#---------------------------------------------------------------------	
	public function feedback_Post()
	{
		$data=$this->input->post();
		//$data = json_decode(file_get_contents("php://input"),true);
		$insertarr=array('id'=>time()."_".$data['user_id'],'user_id'=>$data['user_id'],'subject'=>$data['subject'],'comment'=>$data['comment'],'post_date'=>date("Y-m-d H:i:s")); 
		$result=$this->Common_model->commonInsert("feedback",$insertarr);
		if($result == 0)
		{
			$this->response(array('status'=>'1','message'=>'Thanks for your comment',
			REST_Controller::HTTP_NOT_FOUND));
		}
		$this->response(array( 'status'=>'0','message'=>'Failed Please try again',
		REST_Controller::HTTP_NOT_FOUND));
	}
	#--------------------------------------------------------------------
	# function for piracylinks
	#---------------------------------------------------------------------	
	public function piracylinks_Post()
	{
		$data=$this->input->post();
		//$data = json_decode(file_get_contents("php://input"),true);
		$insertarr=array('id'=>time()."_".$data['user_id'],'user_id'=>$data['user_id'],'movie_name'=>$data['movie_name'],'link'=>$data['link'],'post_date'=>date("Y-m-d H:i:s")); 
		$result=$this->Common_model->commonInsert("piracylinks",$insertarr);
		if($result == 0)
		{
			$this->response(array('status'=>'1','message'=>'Thanks for your help',
			REST_Controller::HTTP_NOT_FOUND));
		}
		$this->response(array( 'status'=>'0','message'=>'Failed Please try again',
		REST_Controller::HTTP_NOT_FOUND));
	}
	  /*public function signUp_post()
	  {
		  $data=$this->input->post();
		  //$data = json_decode(file_get_contents("php://input"),true);
		  print_r($data);exit;
		  //$fullName=$data['fullname'];
		  $mobileNumber=$data['mobilenumber'];
		  $email= $data['email'];
		  $password=$data['password'];
		  $pwd=md5($password);
		  // echo $pwd;
		   $varification_code=substr(uniqid('', true), -5);
		   $today = date("Y-m-d H:i:s");
		   $source = 'web';
		   $status = '2';
		   $txtAddress ='';
		   $txtCity = '';
		   $txtState = '';
		   $txtPincode = '';
		   // $tandc=$data['check'];
		   $tandc='';
		   $mac_addr = "";
		   $referred_by = 0;
		   $deviceid='';
		   $ip_addr = $this->input->ip_address();
		   @$txtReferralCode=$data['refcode'];
		   if($this->Register_model->check_user($email)){
			$referred_by = 0;
		   if(!empty($txtReferralCode)){
			    $referred_by =$this->Register_model->getUidByCode($txtReferralCode);
			    if($referred_by == 0){ 
				  $this->response(array('status'=>'2', 'reg'=>'Enter valid referal code'));  
				exit;
				}
		    }
		   $userid =$this->Register_model->addUser($fullName,$email,$mobileNumber,$txtAddress, $txtCity, $txtState, $txtPincode, $pwd, $tandc, $varification_code, $today, $source, $status, $ip_addr, $mac_addr, $referred_by);
		   if(intval($userid) >0){			
					$reference_code =$this->Register_model->addReferenceCode($userid);
					//$this->Register_model->regMailData($fullName,$email,$mobileNumber,$pwd,'');
					$sms_message =$this->Register_model->RegSMSMSG($reference_code);
				    $this->Register_model->sendSMS($mobileNumber,$sms_message);
				    //$encode = $this->Register_model->encrypt($userid);
		           $this->response(array('status'=>'1', 'reg'=>'Register Successfully'));  
	          }
		}			  
           else{ 
	     $this->response(array('status'=>'0', 'reg'=>'Registration fail please try again...'));  
	    }	*/ 
						
				 
	
	
	 
}
