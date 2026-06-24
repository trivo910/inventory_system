<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends CI_Model {
	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}
	//UPDATE SMS API
	public function api_update(){
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
		$store_id = get_current_store_id();
		$this->db->trans_begin();
		if($hidden_rowcount>0){
		$this->db->query("delete from db_emailapi where store_id=".$store_id);
		$this->db->query("ALTER TABLE db_emailapi AUTO_INCREMENT = 1");
			for($i=1; $i<=$hidden_rowcount; $i++){
				if(isset($_POST['info_'.$i])){
					$info 	 	= $_POST['info_'.$i];
					$key 	 	= $_POST['key_'.$i];
					$key_value 	= $_POST['key_val_'.$i];
					
					$q1=$this->db->query("insert into db_emailapi(
								info,`key`,key_value,store_id)
								values(
								'$info',
								'$key',
								'$key_value',
								$store_id
							)");
					if(!$q1){
						return "failed";
					}

				}//if end()
			}//for end()	
		}

		$q2=$this->db->query("update db_store set email_status=$email_status where id=".$store_id);
		if(!$q2){
			return "failed";
		}

		//save Twilio SMS API
		$twilio = array('account_sid' => $account_sid,'auth_token'=>$auth_token,'twilio_phone'=>$twilio_phone );
		$q1=$this->db->select("*")->where("store_id",$store_id)->get("db_twilio");
        if($q1->num_rows()>0){
          $q2 = $this->db->where("store_id",$store_id)->update("db_twilio",$twilio);
        }
        else{
        	$twilio = array_merge($twilio,array('store_id' => $store_id));
        	$q2=$this->db->insert("db_twilio",$twilio);
        }

        if(!$q2){
        	return "failed";
        }

			$this->session->set_flashdata('success', 'Record Successfully Saved!!');
			$this->db->trans_commit();
		    return "success";
	}

	//Send email
	public function send_email(array $content){

		extract($this->xss_html_filter($content));

		//Validate
		if(empty($to)){
			return "You forgot to add Receipient Email Address";
		}
		if(empty($subject)){
			return "Email Subject should be empty";
		}
		if(empty($subject)){
			return "Email Subject should be empty";
		}

		//is SaaS module enabled ?
		$store_id = (store_module()) ? 1 : get_current_store_id();

		$store_rec = get_store_details($store_id);

		$smtp_status=$store_rec->smtp_status;


		//If SMTP active
		if($smtp_status){
					$config = Array(
				  'protocol' => 'smtp',
				  'smtp_host' => $store_rec->smtp_host,
				  'smtp_port' => $store_rec->smtp_port,
				  'smtp_user' => $store_rec->smtp_user,
				  'smtp_pass' => $store_rec->smtp_pass,
				  'crlf' => "\r\n",
				  'newline' => "\r\n"
				);

		    //Load email library
		    $this->load->library('email',$config);
		    
		    $this->email->to($to);
		    $this->email->from($store_rec->smtp_user,$store_rec->store_name);

		    //Email content
		    $this->email->subject($subject);
		    $this->email->message($message);

		    //Send email
		    if($this->email->send()){
		        return true;
		    }
		    else{
		        return "Failed to send Email!";
		    }

		    //echo $this->email->print_debugger();	
		}//If SMTP enabled
		else{
			//Send trough regular email method

			if(mail($to, $subject, $message)){
				return true;
			}
			else{
				return "Failed to send Email!";
			}


		}

		

	}

}