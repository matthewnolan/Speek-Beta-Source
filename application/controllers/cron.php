<?php
/**
 *  Checks if call has ended and sends an email to participants
 *  Excecuted on a cron job
 *  @author : Joseph A. Luzquinos
 */
class Cron extends CI_Controller {
	
	public function __Construct() {
	    parent::__Construct();
       
        $this->load->library('speek');
        $this->load->helper('email');    
	}
    
    /**
     * Excecuted as a cron job
     * Sends emails to participants that are inactive, but had participated in a call.
     */
    public function end_call_email()
    {
        $query = $this->db->get_where('history',array('email_sent' => NULL));
        
        #loop on participants where emails have not beeen sent
        foreach ($query->result() as $participant){
        
            if(($this->call_has_ended($participant->call_id) == TRUE) && valid_email($participant->email))
            {
                #send email
                $this->send_email($participant->email);
                
                #update email_sent, update time
                $data = array(
                    'email_sent' => TRUE,
                    'updated_at' => date('Y-m-d H:i:s')
                    );
                $this->db->where('id', $participant->id);
                $this->db->update('history', $data);
                
            }
        }

    }
    
    /**
     * Send Email
     * @param  : string
     * @return : boolean
     */
    private function send_email($email)
    {
        $subject = 'Your call at Speek has ended';
        $message = 'This is the message you get after the call. Just delete it.';
        
        if (valid_email($email))
        {
            if(send_email($email, $subject, $message))
            {
                #Message Sent
                return TRUE;
            }
            else
            {
                #Message not sent';
                return FALSE;
            }   
        }
        else
        {
            #Invalid Email Address
            return FALSE;
        }
    }
    
    /**
     * Checks if there are active caller in a call id
     * @param  : string
     * @return : boolean
     */
    private function call_has_ended($call_id)
    {
        #Please register and apply for key at http://developer.speek.com/member/register
        $api_key = $this->config->item('speek_api_key');
        
        #The call id returned to you in the response when you placed the call
        $format = 'json';
        
        #A call id
        $call_id = $call_id;
          
        #call the function 
        $respArr  = $this->speek->getParticipantStatus($api_key,$format,$call_id);
        
        $respArr = json_decode($respArr);
        $respArr = $respArr->results->participants;
        
        #if $active_calls == 0 the conversation has ended
        $acive_calls = 0; 
        foreach ($respArr as $value) {
           $acive_calls += $value->active;
        }
        
        #return TRUE if there are no active callers
        return ($acive_calls == 0 ? TRUE : FALSE);
    }
    
}
#end cron.php file