<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Join extends CI_Controller {

       private $API_KEY;
       private $FORMAT_JSON='json';

       private $RETRIES=3;
       private $HUNT='';
       private $LONGITUDE='';
       private $LATITUDE='';

       private $DESCRIPTION_IPHONE="call now from iPhone-php";

       private $GREETING        = '0';
       private $GREETING_METHOD = 'text';
       private $GREETING_TEXT   = 'test greeting';
       private $GREETING_LINK   ='';

       private $DO_RECORDING    = '1';
       private $MUSIC_ON_HOLD   = '1';
       private $SOUND_ON_IN_OUT = '1';
       private $EXIT_ON_LEAVE   = '1';
       private $CALL_NAME = '';
       private $RSVP = '';
       
       public function __Construct()
       {
		parent::__Construct();

		$this->load->library('speek');
		$this->load->library('user_agent');
		$this->load->model('conference_model');
                $this->load->model('ajax_model'); 
		$this->API_KEY = $this->config->item('speek_api_key'); 
       }
	
	private function getCallId($slug) 
        { 
             if ($this->ajax_model->first_call_check($slug) == FALSE) 
             { 
		return NULL; 
             } 
	     else {
                 $slug_info = $this->ajax_model->get_slug_info($slug);

                return $slug_info->call_id; 
             }
        } 
       
        public function isiPhoneRequest()   
        { 
	    //return $this->agent->is_mobile('iphone');		//doesn't work for iphone programmatic call
            return TRUE; 
        }
        public function testURL()
        { 
		echo "<script> window.location='speek_headstand:' </script>";
        } 
        public function doIt()   
        {   
             if ($this->isiPhoneRequest()) { 
                 $slug = $this->input->get('slug'); 
                 $phone_number = $this->input->get('phone_number'); 

	         $call_id = $this->getCallId($slug); 			

                 if ($call_id != NULL) { 
                     $this->doSubsequentCallForSlug($call_id,$slug,$phone_number);
                 } 
                 else { 
                     $this->doFirstCallForSlug($slug,$phone_number); 
                 } 
             }  else { 
                 echo 'request only supported on iPhone..sorry..'; 
             } 
              
        } 	     
                
        private function doFirstCallForSlug($slug, $phone_number) { 
            $respArr = $this->speek->callNow($this->API_KEY,  
                                             $this->FORMAT_JSON, 
                                             $this->DESCRIPTION_IPHONE, 
                                             $phone_number, 
                                             $phone_number, 
                                             $this->MUSIC_ON_HOLD, 
                                             $this->GREETING, 
                                             $this->GREETING_METHOD, 
                                             $this->GREETING_TEXT, 
                                             $this->GREETING_LINK, 
                                             $this->DO_RECORDING, 
                                             $this->SOUND_ON_IN_OUT, 
                                             $this->EXIT_ON_LEAVE, 
                                             $this->CALL_NAME, 
                                             $this->RSVP);          
            echo $respArr; 
            $response = json_decode($respArr);
      	    $callId = $response->results->call_id; 
            $this->ajax_model->add_call_id($slug, $callId);
	    if ($this->addCaller($callId,$phone_number) == TRUE) 
            { 
               print_r($respArr); 
               exit; 
            }
       }
 
        	
       private function doSubsequentCallForSlug($call_id,$slug, $phone_number) 
       {
	  $respArr  = $this->speek->addParticipant(
                                                   $this->API_KEY, 
                                                   $this->FORMAT_JSON, 
                                                   $phone_number,  
                                                   $call_id,  
                                                   $this->RETRIES, 
                                                   $this->HUNT,
                                                   $this->LONGITUDE,
                                                   $this->LATITUDE);
          $response = json_decode($respArr);
          echo $respArr;  
          
        
       if  ($this->addCaller($call_id, $phone_number) == TRUE) {
        #print the responce
         print_r($respArr);
         exit;
       }

      }

      private function addCaller($call_id, $phone_number) 
      {   
       	  $data['call_id']            = $call_id; 
          $data['method_identifier']  = $phone_number;
          $data['method'] = 'iPhone'; 
          $ret = $this->ajax_model->add_caller($data); 
      } 




}

/* End of file join.php */
