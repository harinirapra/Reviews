<?php
class Register_model extends CI_Model {
	#--------------------------------------------------------------------
	# function for updating apikey
	#---------------------------------------------------------------------	
	public function updateKey($uid,$randomString){
		$data=array('key'=>$randomString);
		$this->db->where('user_id',$uid);
		return $res=$this->db->update("users",$data);  
	 }
	 /*public function deleteUserKey($uid){
		  $data=array('key'=>'');
		  $this->db->where('uid',$uid);
		  return $res=$this->db->update('users',$data);
	 }*/
        #--------------------------------------------------------------------
	# function for Login Authentication
	#---------------------------------------------------------------------	
	function check_login($email,$password){
		$password = md5($password);
		$this->db->select("*");
		$this->db->from("users");
		$this->db->where('email',$email);
		$this->db->where('password',$password);
		$query=$this->db->get();
		if($query->num_rows()==1)
		{
		 return $query->result_array();	
		}
		 
	}
	#--------------------------------------------------------------------
	# function for checking reference code
	#---------------------------------------------------------------------
	 public function checkverCode($vCode)
	 {
		$this->db->select('varification_code');
		$this->db->from('users');
		$this->db->where('varification_code',$vCode);
		$q=$this->db->get();
		if($q->num_rows()> 0 ){
		return 1;
		}
		else{
		return 0;
		}	 
	 }
	#--------------------------------------------------------------------
	# function for Add user in signup
	#---------------------------------------------------------------------	
	public function addUser($name,$email,$mobileNumber,$pwd,$gender,$dob,$varification_code, $today,$status, $ip_addr){
		$sql="insert into users (email,user_name,user_mobile,password,gender,dob,varification_code,status,
		created_date)values ('$email','$name','$mobileNumber', '$pwd', '$gender','$dob','$varification_code','$status','$today')";
		$result=$this->db->query($sql);
		if($result){
			$user_id = $this->db->insert_id();
			return $user_id;
		}else{
			return 0;
		}
	}
	#--------------------------------------------------------------------
	# function for change password
	#---------------------------------------------------------------------
	public function changepassword($password,$userid)
	{
		$pwd = md5($password);
		$data=array('password'=>$pwd);
		$this->db->where('user_id',$userid);
		$this->db->update('users',$data);
		$q=$this->db->affected_rows();
		if($q==1){
			return 1;
		}
		else{
			return 0;
		}
	}
	function userbyid($uid)
	{
		//$password = md5($password);		
		//echo "SELECT * FROM `users` WHERE `email`='".$username."' and `password`='".$password."'"; exit;
		$str_query = "SELECT * FROM `users` WHERE `uid`=?";
		$result = $this->db->query($str_query,array($uid));		
		return $result;
	}
	public function check_user($username){
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where('email',$username);		
		$q=$this->db->get();		//return $q->row();
		if($q->num_rows()==0){
			return true;
		}
		else{
			return false;
		}
	}
	public function checkmail($email){
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where('email',$email);		
		$q=$this->db->get();		//return $q->row();
		if($q->num_rows()==0){
			return 1;
		}
		else{
			return 0;
		}
	}
	public function checkMobNo($phoneno){
		$this->db->select('mobile');
		$this->db->from('users');
		$this->db->where('mobile',$phoneno);		
		$q=$this->db->get();		//return $q->row();
		if($q->num_rows()==0){
			return 1;
		}
		else{
			return 0;
		}
	}
	/* *** REFERENCE CODE *** */
	public function addReferenceCode($uid){
		if(intval($uid) > 0){
			$reference_code = substr(uniqid('', true), -5);
			$count = $this->db->select('count(*) as cnt')->where(array('reference_code'=>$reference_code))->get('users')->row()->cnt;
			if($count == 0){
				$str = "UPDATE `users` SET `reference_code`=? where `uid`=? and `reference_code`=''";
				$rr = $this->db->query($str, array($reference_code, $uid));
				if($rr){
					return $reference_code;
				}else{
					$this->addReferenceCode($uid);	
				}
			}else{
				$this->addReferenceCode($uid);
			}
		}
	}
	/* *** REFERENCE CODE *** */
	public function regMailData($fullName, $email,$mobileNumber,$pwd,$code){
		$msg=$this->reg_mail_subject($fullName,$email,$mobileNumber,$pwd,$code);	 
		$sub = "GENFONE: Registration details";
		$to = $email;
		$from = "info@genfone.com";
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "To: webmaster <".$to.">\r\n";
		$headers .= "From: ".$from;
		/* and now mail it */
		if(mail($to, $sub, $msg, $headers)){
			//return "Mail Sent to ".$to." Successfully";
		}else{
			//return "Error Occured While sending Email. Please try Again";
		}
	}
	#--------------------------------------------------------------------
	# function for registration message
	#---------------------------------------------------------------------	
	public function RegSMSMSG($varification_code){
		$msg = "Welcome To Reviews Round! Your Varification Code is ".$varification_code."\n Download our mobile app https://goo.gl/8seSq9";
		return $msg;
	}
	#--------------------------------------------------------------------
	# function for send sms
	#---------------------------------------------------------------------	
	public function sendSMS($mobileno,$msg){
		$sender="GENFON";
		$url = "http://smslogin.mobi/spanelv2/api.php?username=genfon&password=534957&to=".$mobileno."&from=$sender&message=".urlencode($msg); 
		$ret = file($url);
		$response = json_encode($ret);
		$this->load->model("Sms_model");
		$this->Sms_model->addSentMessage($mobileno,$msg,$response);		
	}
	public function encrypt($string)
	{
		$cipher = $this->encrypt->encode($string);
		 return  strtr($cipher,array(
                        '+' => '.',
                        '=' => '-',
                       '/' => '~'));
	}
	 public function reg_mail_subject($name, $loginname, $pwd, $code){
		$msg = '<center><div style="border: 10px solid #0094B4; width:620px;  -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px;"><table width="600" background="#FFFFFF" style="text-align:left;" cellpadding="0" cellspacing="0">
<tr><td height="18" width="28" style="border-bottom:0px solid #e4e4e4;"></td><td height="18" width="183"></td><td height="18" width="387" style="border-bottom:1px solid #e4e4e4;"></td></tr><tr><td height="2" width="28" style="border-bottom:1px solid #e4e4e4;"></td><td height="2" width="183"><div style="line-height: 0px; font-size: 1px; position: absolute;">&nbsp;</div></td><td height="2" width="387" style="border-bottom:1px solid #e4e4e4;"><div style="line-height: 0px; font-size: 1px; position: absolute;">&nbsp;</div></td></tr><tr><td ></td><td width="60" bgcolor="#FFFFFF" style="border-top:1px solid #FFF; text-align:center;" height="60" valign="middle"><span style="font-size:25px; font-family:Trebuchet MS, Verdana, Arial; color:#2e8a3b;"><a href="'.base_url().'"><img src="'.base_url().'assets/images/logo.png " title="Genfone" alt="Genfone"></a></span></td><td align="center"><span style="color:#000; font-size:18px; font-family:Trebuchet MS, Verdana, Arial;">Thank you for Registering with Us.</span></td></tr><tr><td height="2" width="28" style="border-top:1px solid #e4e4e4; border-bottom:1px solid #e4e4e4;"></td><td height="2" width="183"><div style="line-height: 0px; font-size: 1px; position: absolute;">&nbsp;</div></td><td height="2" style="border-top:1px solid #e4e4e4; border-bottom:1px solid #e4e4e4;"><div style="line-height: 0px; font-size: 1px; position: absolute;">&nbsp;</div></td></tr><tr>	<td colspan="3"><br /><table cellpadding="0" cellspacing="0"><tr><td width="15"><div style="line-height: 0px; font-size: 1px; position: absolute;">&nbsp;</div></td>	<td width="325" style="padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;" valign="top"><p>Dear '.$name.',<h4>Welcome to Genfone.com!</h4><p align="justify">Thank you for your registration and choosing us as your recharge service provider. At Genfone you can recharge your Mobile (Prepaid and Postpaid), DTH and DataCard and pay bill in an easy, secure and hassle free.<br />Here are the credentials you have provided during the registration process. Please use them when prompted to log in::</p><br/><div style="background-color:#00B37C; font-size:12px; padding:10px;"><strong>Login User Name :</strong> '.$loginname.' <br/>';
	if(trim($pwd)!=''){
		$msg .= '<strong>Password :</strong> '.$pwd.' <br/>';
	}
	if(trim($code)!=''){
		$msg .= '<strong>Verfication Code :</strong> '.$code.' <br/>';
	}
   $msg .= '</div><p>Once you are <a href="'.base_url().'">logged in to your account</a>, you will be able to:</p><ul><li>Proceed through checkout faster when making a purchase.</li><li> Check the status of your orders.</li><li> View previous orders.</li><li> Make recharges even faster using RechargeDone Credits.</li><li> Make changes to your account information.</li><li>  Change your password.</li></ul>------------------------------------------------------------<br />Best Regards,<br/><br/><a href="'.base_url().'">Genfone Technologies Pvt Ltd.</a><br/><br/>This welcome email was sent to {!email} because you recently signed up for occasional messages from me at '.base_url().'</td><td style="border-left:1px solid #e4e4e4; padding-left:15px;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #e4e4e4; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><tr><td><div style="font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold; padding-bottom:10px;">Add Us To Your Address Book</div><img src="'.base_url().'assets/images/addressbook.gif" align="right" style="padding-left:10px; padding-top:10px; padding-bottom:10px;" alt=""/><p>To help ensure that you receive all email messages consistently in your inbox with images displayed, please add this address to your address book or contacts list: <strong>[info@genfone.com]</strong>.</p><br /></td></tr></table><br /><table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #e4e4e4; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><tr><td><div style="font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold; padding-bottom:10px;">Have Any Questions?</div><img src="'.base_url().'assets/images/penpaper.gif" align="right" style="padding-left:10px; padding-top:10px; padding-bottom:10px;" alt=""/><p>Don\'t hesitate to hit the reply button to any of the messages you receive.</p><br /></td></tr></table><br /><table cellpadding="0" width="100%" cellspacing="0" style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><tr><td><div style="font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold; padding-bottom:10px;">Our mobile APPs</div><a href="https://goo.gl/8seSq9"><img src="'.base_url().'assets/images/android.png" align="right" style="padding-left:10px; padding-top:10px; padding-bottom:10px;" alt=""/><p>Download our android mobile app.</p></a><br /></td></tr></table></td></tr></table></td></tr></table><br /><table cellpadding="0" style="border-top:1px solid #e4e4e4; text-align:center; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;" cellspacing="0" width="600"><tr><td height="2" style="border-bottom:1px solid #e4e4e4;"><div style="line-height: 0px; font-size: 1px; position: absolute;">&nbsp;</div></td></tr><td style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><br />Flat NO 807, Manjeera Majestic Commercial, Near JNTU, KPHB Kukat Pally, Hyderabad, India 500072<br /><br /></td></tr></table></div></center>'; 
   return $msg;
   	 }
	
	  public function updateRefCode($refCode,$uid)
	 {
		 $data=array('reference_code'=>$refCode);
         $this->db->where('uid',$uid);
         $this->db->update('users',$data);
		 $q=$this->db->affected_rows();
		if($q==1){
			return 1;
		}
		else{
			return 0;
		}
		 
	 }
	 public function getUidByCode($referral_code){
		$result = @$this->db->select('uid')->where(array('referral_code'=>$referral_code))->get("users")->row()->uid;
		if(intval($result) > 0){
			return $result;
		}else{
			return 0;
		}
		
	}
	 
	public function getProvider($id, $otype='json'){
			if(trim($id) == ''){
				$id = '1';
			}
			$this->db->select('sno,name');
			$this->db->from('provider');
			$this->db->where('service_id',$id);
			$this->db->where('status','1');
			$result=$this->db->get();
			if($result->num_rows() > 0){
				if($otype == 'web'){
					foreach($result->result() as $row){
						$output .= "<option value='".$row->sno."' class='".$row->company."'>".ucfirst($row->name)."</option>";
					}
				}else if($otype == 'json'){
					    return json_encode($result->result());		
				}
			}
			  
			 return $output;
	}

	public function getCityList()
	{
		$this->db->select('city_id,city_name');
		$this->db->from('city_list');
		$result=$this->db->get();
	    return json_encode($result->result());
	}
	/** User List */
	 // public function  profiledata($userid)
	 // {
	 
	 // $this->db->select('*');
	 // $this->db->from('users');
	 // $this->db->where('uid',$userid);
	 // $result=$this->db->get();
	 // echo  json_encode($result->result());
	 // }
	 
	 // opertorname by operater id */ // 
	public function  operatoridbyname($operator)
	{
		$this->db->select('name');
	    $this->db->from('provider');
	    $this->db->where('sno',$operator);
	    $result=$this->db->get();
	    return $result->result_array();
	   
	}
	 
		public function forgetpassword($txtUserName)
		{

                              $result='';
		if(!empty($txtUserName) && preg_match('/^[0-9]{10}+$/', $txtUserName)){
			 $userinfo = $this->Common_model->userifnobymobile($txtUserName);
			 
			 if($userinfo->num_rows() == 1){
			  $userinfo = $userinfo->result();
			  $userinfo = $userinfo[0];
			  if($userinfo->status == '1'){
			   $this->load->model("Register_model");
			   $code=substr(uniqid('', true), -6);
			   $num = $this->Register_model->userpassword($userinfo->uid, $code);
			   if($num == 1){
				$userinfos = $this->Common_model->userifnobymobile($txtUserName);
				$userinfop = $userinfos->result();
			    $userinfop = $userinfop[0];
				//echo $url; exit;
				$result=$this->Common_model->forgotpwdMailDatap($userinfop->name, $userinfop->email, $code);
				
			   }else{
				$result=0;
			   }
			  }else{
			  
			   $result=0;
			  }
			 }else{
			  $result=0;
			 }
			}
			else if(!filter_var($txtUserName, FILTER_VALIDATE_EMAIL) === false){

			 $userinfoz = $this->Common_model->userifnobyemail($txtUserName);
			 
			 if($userinfoz->num_rows() == 1){
			  $userinfoz = $userinfoz->result();
			  $userinfo = $userinfoz[0];

			  if($userinfo->status == '1'){
			   $this->load->model("Register_model");
			   $code=substr(uniqid('', true), -6);
			   $num = $this->Register_model->userpassword($userinfo->uid, $code);

			   if($num == 1){
				$userinfos = $this->Common_model->userifnobyemail($txtUserName);
				$userinfop = $userinfos->result();
			    $userinfop = $userinfop[0];
				
				$result=$this->Common_model->forgotpwdMailDatap($userinfop->name, $userinfop->email, $code);
				
				
			   }else{
				$result=0;
			   }
			  }else{
			  
			   $result=0;
			  }
			 }else{
			  $result=0;
			 }
			}
			
			
			
			
			else{
			 $result=0;
			}
			   return $result;
		}
	public function usercode($uid , $code){
	  $query = $this->db->query("UPDATE `users` SET `varification_code` = '$code'  WHERE `uid` = '$uid' ");
	  //$this->db->query($str, array($code, $uid));
	  
	  $num = $this->db->affected_rows();
	   return $num;
	 }
public function transactions($userid,$from_date,$to_date) 
	{
	 if($from_date!='' &&$to_date!='')
	 {
	$where = ($from_date == $to_date)?" DATE(add_date) ='$from_date'":" DATE(add_date) between '$from_date' and '$to_date'";
		$query=$this->db->query( "select payment_history.*,DATE_FORMAT(payment_history.add_date,'%b %d %Y %h:%i %p') as add_datez,(select paystatus from mobileorders where  payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0 and payment_history.`transaction_type` = 'Rechargepayment' ) as paystatus,(select rstatus from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as rstatus,(select mobnumber from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as mobnumber,(select spid from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as spid,( select status from walletorder where payment_history.`payment_id` = walletorder.`sno` and `payment_id`>0 and payment_history.`transaction_type` = 'PAYMENT') as status from payment_history where `user_id`= '$userid' and `transaction_type`='Recharge' and $where order by id desc "); 
	 }
	 else {
		 $query=$this->db->query( "select payment_history.*,DATE_FORMAT(payment_history.add_date,'%b %d %Y %h:%i %p') as add_datez,(select paystatus from mobileorders where  payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0 and payment_history.`transaction_type` = 'Rechargepayment' ) as paystatus,(select rstatus from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as rstatus,(select mobnumber from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as mobnumber,(select spid from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as spid,( select status from walletorder where payment_history.`payment_id` = walletorder.`sno` and `payment_id`>0 and payment_history.`transaction_type` = 'PAYMENT') as status from payment_history where `user_id`= '$userid' and `transaction_type`='Recharge' or  `transaction_type`='Rechargepayment'  order by id desc limit 0,10");
	 }
	 // echo $this->db->last_query();exit;
	  return $result = $query->result_array();
	}
		/***** User List ***/
	 public function updateProfile($uid, $name, $address, $city, $state, $pincode)
	{
	$data=array('name'=>$name,'address'=>$address,'city'=>$city,'state'=>$state,'pincode'=>$pincode);
	 $this->db->where('uid',$uid);
	 $this->db->update('users',$data);
	 $num = $this->db->affected_rows();
	 return $num;
	
      }
	  /***** User List ***/
	public function  profiledata($userid)
	{
	
	$this->db->select('*');
	$this->db->from('users');
	$this->db->where('uid',$userid);
	$result=$this->db->get();
	echo  json_encode($result->result_array());
	}
	
	public function getComplaintList($uid)
	{
		$str = "select * from complain  where user_id = '".$uid."'";
			$result = $this->db->query($str);
			return $result->result_array();	
	}
	public function saveComplaint($uid, $type, $rechargeid, $message){
		$complain_date = date("Y-m-d");
		$complain_status='Pending';
		$sql="insert into complain (recharge_id,user_id,complain_date,complain_status,message,complain_type)
		values ('$rechargeid','$uid','$complain_date', '$complain_status', '$message','$type')";
		//echo $this->db->last_query();
		$result=$this->db->query($sql);
		if($result){
			$userid = $this->db->insert_id();
			
			return $userid;
			
		}else{
			return 0;
		}
	}
	public function getCurrentBalance($userid)
	 {
	 $this->db->select('balance');
	 $this->db->from('payment_history');
	 $this->db->where('user_id',$userid);
	 $this->db->order_by('id', 'DESC');
	 $this->db->limit(1);
	 $result=$this->db->get();
	 echo  json_encode($result->result());
	 
	  
	 }
 public function getcreditlist($uid,$from_date,$to_date)
	 {
		 
	 if($from_date!='' && $to_date!='')
	 {
		 $where = ($from_date == $to_date)?" DATE(odate) ='$from_date'":" DATE(odate) between '$from_date' and '$to_date'";
		 $query =$this->db->query( "SELECT `sno`, `ordertype`, `refund_from`, `uid`, `amount`, `status`, CASE when `status` = '0' then 'Fail' when `status` = '1' then 'Success'  when `status` = '2' then 'Pending'    when `status` = '4' then 'AdminPending' END as status, DATE_FORMAT(odate,'%b %d %Y %h:%i %p') as `odate`, `info` FROM `walletorder` WHERE `uid`= '$uid' and `status` != '2' and $where");  
	 }	
      else
	  {
		  $query =$this->db->query( "SELECT `sno`, `ordertype`, `refund_from`, `uid`, `amount`, `status`, CASE when `status` = '0' then 'Fail' when `status` = '1' then 'Success'  when `status` = '2' then 'Pending'    when `status` = '4' then 'AdminPending' END as status, DATE_FORMAT(odate,'%b %d %Y %h:%i %p') as `odate`, `info` FROM `walletorder` WHERE `uid`= '$uid' and `status` != '2' limit 0,10");  
		  
	  }		  
	  return $result = $query->result_array();
	  
	 }
  public function userpassword($uid , $code){
	 $password=md5($code);
	  $query = $this->db->query("UPDATE `users` SET `password` = '$password'  WHERE `uid` = '$uid' ");
	  //$this->db->query($str, array($code, $uid));
	  
	  $num = $this->db->affected_rows();
	   return $num;
	 }

 public function accountsTransactions($user_id,$from_date,$to_date)
	 {
		 if($from_date!='' && $to_date!='')
		 {
	  $where = ($from_date == $to_date)?" DATE(add_date) ='$from_date'":" DATE(add_date) between '$from_date' and '$to_date'";
     $query=$this->db->query("select payment_history.id, payment_history.user_id,payment_history.payment_id,payment_history.payment_id,payment_history.add_date,payment_history.description,payment_history.debit_amount as Amount,payment_history.balance as balance,
           DATE_FORMAT(payment_history.add_date,'%b %d %Y %h:%i %p') as add_datez,(select paystatus from mobileorders where  payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0 and payment_history.`transaction_type` = 'Rechargepayment' ) as paystatus,(select rstatus from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as rstatus,(select mobnumber from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as mobnumber,(select spid from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as spid,( select status from walletorder where payment_history.`payment_id` = walletorder.`sno` and `payment_id`>0 and payment_history.`transaction_type` = 'PAYMENT') as status from payment_history where `user_id`= '$user_id' and `transaction_type`='Recharge' and $where");
		 }
		 else
		 {
			 
			 $query=$this->db->query("select payment_history.id, payment_history.user_id,payment_history.payment_id,payment_history.payment_id,payment_history.add_date,payment_history.description,payment_history.debit_amount as Amount,payment_history.balance as balance,
				DATE_FORMAT(payment_history.add_date,'%b %d %Y %h:%i %p') as add_datez,(select paystatus from mobileorders where  payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0 and payment_history.`transaction_type` = 'Rechargepayment' ) as paystatus,(select rstatus from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as rstatus,(select mobnumber from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as mobnumber,(select spid from mobileorders where payment_history.`recharge_id` = mobileorders.`sno` and `recharge_id` > 0  and payment_history.`transaction_type` = 'Recharge') as spid,( select status from walletorder where payment_history.`payment_id` = walletorder.`sno` and `payment_id`>0 and payment_history.`transaction_type` = 'PAYMENT') as status from payment_history where `user_id`= '$user_id' and `transaction_type`='Recharge'   order by id desc limit 0,10");
			 
		 }
		  //echo $this->db->last_query();exit;
		return $result = $query->result_array();
		 
	 }
	
	 public function contactus($email,$fname,$mobile,$msg){
		$html="<strong>User Details</strong></br></br>";

                      
$html.='<table style="font-family: arial,sans-serif;border-collapse: collapse; width: 100%;">

  <tr>
    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><strong>Name :</strong></td>
    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.$fname.'</td>
   
  </tr>
  <tr>
    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><strong>Mobile :</strong></td>
    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.$mobile.'</td>
   
  </tr>
<tr>
    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><strong>Message : </strong></td>
    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.$msg.'</td>
   
  </tr>
 
</table>';
		    $this->load->library('email');
			$this->email->from($email);
			$this->email->to('muddamkrishna@gmail.com');
			$this->email->set_mailtype("html"); 
			$this->email->subject("GENFONE :: User Details");
			
			$this->email->message($html);
			if($this->email->send())
			{
			    return 1;
			}
			else
			{
				return 0;
			}
	 }
}
?>
