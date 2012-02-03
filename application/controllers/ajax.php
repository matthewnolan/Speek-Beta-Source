<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {
    
    private $API_KEY;
    private $API_FORMAT             = 'json';
    private $API_CALL_ID;
    private $API_DESCRIPTION        = 'this is a call now request from php';
    private $API_NUMBERS            = '';
    private $API_ORGANIZER          = '';
    private $API_MUSIC_ON_HOLD      = '1';
    private $API_GREETING           = '1';
    private $API_GREETING_METHOD    = 'audio';
    private $API_GREETING_TEXT      = 'this is a test greeting message from php';
    private $API_GREETING_LINK      = '';
    private $API_RECORDING          = '1';
    private $API_SOUND_ON_INOUT     = '1';
    private $API_EXIT_ON_LEAVE      = '0';
    private $API_CALL_NAME          = '';
    private $API_RSVP               = '';
    private $API_RETRY              = '';
    private $API_HUNT               = '';
    private $API_LONGITUDE          = '';
    private $API_LATITUDE           = '';

	public function __Construct()
	{
		parent::__Construct();
        
        #load libraries
		$this->load->library('speek');
		$this->load->model('ajax_model');
		$this->load->model('conference_model');
		$this->load->helper('recaptchalib');
        $this->load->helper('email');
        
        #add values to vars
        $this->API_KEY           = $this->config->item('speek_api_key');
        $this->API_GREETING_LINK = base_url().'assets/music/guns_n_roses-welcome_to_the_jungle.mp3';
        
	}
	
	public function create_slug()
	{
		# initialize response
		$response = array('message' => null, 'error' => null);
				
				if($this->input->post('email') != null && $this->input->post('captcha_expected') != "yes") // bot detection via honeypot method
				{
					$response['error'] = 'Bot detected';
					$response['code']  = 4;
					echo json_encode($response);
					exit;
				}
				
				#validate captcha
				$response = $this->validateCaptcha($response, $this->input);
				
				if ($response['message'] === false)
				{
					echo json_encode($response);
					exit;
				}
				
				#Captcha is valid, now querying database to see if desired slug is unique
				$slug   = $this->input->get('slug');
                $gfc_id = $this->input->get('gfc_id');
		
				   
				if($this->ajax_model->check_duplicate($slug) == TRUE){

					if ($this->ajax_model->create_slug($slug) == TRUE){
					    
                            if($gfc_id != NULL){
                                $this->ajax_model->add_gfc_id($slug, $gfc_id);
                            }
                            
						#get slug info from DB
                        $slug_info = $this->ajax_model->get_slug_info($slug);
                        
                        $response['conference_id'] = $slug_info->call_id;
						$response['message']       = TRUE;
						$response['code']          = 0;
						$response['slug']          = $slug;
                        $response['expires_at']    = $slug_info->expires_at;
                        
					} else {
						$response['message'] = FALSE;
						$response['code']    = 1;
						$response['error']   = "Could not create conference";
					}
		
				} else {
					$response['message'] = FALSE;
					$response['code']    = 2;
					$response['error']   = "URL already in use.";
				}

		echo json_encode($response);
        exit;
		
	}

	public function set_conference_user_id()
	{
		$response = array('success' => false, 'error' => false);
		
		$gfc_id        = $this->input->get('gfc_id'); // google friend connect id
		$conference_id = $this->input->get('conference_id'); // conference id
		
		$conference =  $this->conference_model->findOneById($conference_id); // finding conference object
		$conference->setGfcId($gfc_id); // setting google friend connect id
		$conference->setExpiresAt(null); // setting expiration date to null since user has logged in

			if ($conference->save()) // saving conference object to database
			{
				$response['success']       = TRUE;
				$response['conference_id'] = $conference->getId();
				$response['gfc_id']        = $gfc_id;
			}
			else
			{
			    $response['success'] = FALSE;    
				$response['error']   = 'Could not save conference';
			}
	
		echo json_encode($response);
	}

	private function validateCaptcha($response, $input)
	{
		if ($input->post('captcha_expected') != "yes")
		{
			return $response;
		}
		
		$recaptcha_response = recaptcha_check_answer(
			  					$this->config->item('recaptcha_private_key'), 
			  					$input->ip_address(),
			  					$input->post('recaptcha_challenge_field'),
			  					$input->post('recaptcha_response_field'));
			
		if (!$recaptcha_response->is_valid)
		{
			$response['message'] = false;
			$response['code'] = 3;
			$response['error'] = $recaptcha_response->error;
		}
		
		return $response;
		
	}

	public function check_first_caller()
	{
		$slug = $this->input->get('slug');
        
		if($this->ajax_model->first_call_check($slug) == FALSE){
		    
            $response['message'] = FALSE;
            $response['error']   = 'no call id yet';
            echo json_encode($response);
            exit;

		}else{

			$conference = $this->ajax_model->get_slug_info($slug);
            
            $response['message'] = TRUE;
            $response['call_id'] = $conference->call_id;
            echo json_encode($response);
            exit;
            
		}

	}
    
    public function get_call_status()
    {
        $slug = trim($this->input->get('slug'));
        if(!$slug){exit;}
        
        $slug_info = $this->ajax_model->get_slug_info($slug);
        
        if($slug_info->call_status == NULL){
            $data['call_status'] = 'active';
        }else{
            $data['call_status'] = $slug_info->call_status;
        }
        
        echo json_encode($data);
        exit;
    }
    
    public function gfc_logged_in()
    {
        $gfc_id = $this->input->get('gfc_id');
        $slug   = $this->input->get('slug');
        
        if($gfc_id == '')
        {
            $data['status'] = FALSE;
            echo json_encode($data);
            exit;
        }
        
        $slug_info = $this->ajax_model->get_slug_info($slug);
        
        $gfc_id_match = ($slug_info->gfc_id == $gfc_id ? TRUE : FALSE);
        
        if($gfc_id_match == TRUE)
        {
            $data['status'] = TRUE;
        }
        else
        {
            $data['status'] = FALSE; 
        }

        echo json_encode($data);
        
    }

    public function show_previous_slugs()
    {
        $gfc_id         = $this->input->post('gfc_id');
        $current_slug   = $this->input->post('current_slug');
        
        $data['current_slug'] = $current_slug;
        $data['slugs'] = $this->ajax_model->get_previous_slugs($gfc_id);
        $this->load->view('main/slug_prev_view', $data);
        
    }
    
    public function show_history()
    {
        $current_slug = $this->input->post('current_slug');
        
        $slug_info = $this->ajax_model->get_slug_info($current_slug);
        
        if($slug_info->call_id == null){
            echo '<p>There are no records for this call.</p>';
            exit;
        }

        #Prep vars
        $this->API_CALL_ID = $slug_info->call_id;
        
        #call the API
        $respArr  = $this->speek->callAnalytics(
            $this->API_KEY,
            $this->API_FORMAT,
            $this->API_CALL_ID
            );
        
        $response = json_decode($respArr);
        $results  = $response->results;
        
        $data['participants'] =  $results->participants;
        $this->load->view('main/slug_history_view', $data);
        
    }
    
    public function delete_slugs()
    {
        $slug   = $this->input->post('slug');
        $gfc_id = $this->input->post('gfc_id');
        
        $data['slugs'] = $this->ajax_model->get_previous_slugs($gfc_id);
        $this->load->view('main/slug_prev_view', $data);
    }
    
    public function current_callers($slug)
    {
        $gfc_id = $this->input->post('gfc_id');
        
        $slug_info = $this->ajax_model->get_current_callers($slug, $gfc_id);
        
        if($slug_info == array())
        {
            exit; 
        }
        if($slug_info->call_id == NULL)
        {
           exit;
        }
        
        #prep vars
        $this->API_CALL_ID = $slug_info->call_id;
        
        #call the API 
        $respArr  = $this->speek->getParticipantStatus(
            $this->API_KEY,
            $this->API_FORMAT,
            $this->API_CALL_ID
            );
        
        $response = json_decode($respArr);
        
        $status = $response->status;
        
        if(!$status){
            exit;
        }
        
        if($status->ok != 1){
            exit;
        }
        
        $results      = $response->results;
        $participants = $results->participants;
        
        $organizer = $participants[0];
                
        $data['organizer_active'] = $organizer->active;
        $data['status']           = $status->ok;
        $data['call_id']          = $this->API_CALL_ID;
        $data['participants']     = $participants;

        $this->load->view('main/current_callers_view', $data);
    }

    public function remove_caller()
    {
        $call_id = $this->input->get('call_id');
        $number  = $this->input->get('number');
        
        #pass local vars
        $this->API_CALL_ID = $call_id;
        $this->API_NUMBERS = $number;
        
        #call the function 
        $respArr  = $this->speek->removeParticipant(
            $this->API_KEY,
            $this->API_FORMAT,
            $this->API_CALL_ID,
            $this->API_NUMBERS
            );
        
        #print the responce
        echo $respArr;
    }

	public function first_caller()
	{
	    $form_name        = $this->input->get('form_name');
	    $form_email       = $this->input->get('form_email');
		$form_call_method = $this->input->get('form_call_method');
		$phone_number	  = $this->input->get('form_call_phone_number');
		$slug             = $this->input->get('slug');
        
        #Change status to processing (fix race condition)
        $this->ajax_model->set_call_status($slug, 'processing');

		switch ($form_call_method) {
			case 'form_call_skype':
				$phone_number = $phone_number.'@skype.com';
				break;
			case 'form_call_google':
				$phone_number = $phone_number.'@gmail.com';
				break;
			default:
				$phone_number = $phone_number;
				break;
		}
		
		#pass local vars        
        $this->API_NUMBERS = $phone_number;
		$this->API_ORGANIZER = $phone_number;
		
		#call the API
        $respArr = $this->speek->callNow(
		  $this->API_KEY,
		  $this->API_FORMAT,
		  $this->API_DESCRIPTION,
		  $this->API_NUMBERS,
		  $this->API_ORGANIZER,
		  $this->API_MUSIC_ON_HOLD,
		  $this->API_GREETING,
		  $this->API_GREETING_METHOD,
		  $this->API_GREETING_TEXT,
		  $this->API_GREETING_LINK,
		  $this->API_RECORDING,
		  $this->API_SOUND_ON_INOUT,
		  $this->API_EXIT_ON_LEAVE,
		  $this->API_CALL_NAME,
		  $this->API_RSVP
          );
        
		#decode the responce
        $response = json_decode($respArr);
        
        #prep data for history
        $data['full_name']         = $form_name;
        $data['email']             = $form_email;
        $data['call_id']           = $response->results->call_id;
        $data['method_identifier'] = $phone_number;
        switch ($form_call_method) {
            case 'form_call_skype':
                $data['method'] = 'Skype';
                break;
            case 'form_call_google':
                $data['method'] = 'Gmail';
                break;
            default:
                $data['method'] = 'Telephone';
                break;
        }
        
       if($this->ajax_model->add_caller($data) == TRUE)
       {
            #Change status to active (fix race condition)
            $this->ajax_model->set_call_status($slug, 'active');
            
            #print the responce
            print_r($respArr);
            exit;
       }
	}

    private function renew_caller($form_call_method, $phone_number, $slug)
    {
        #Change status to processing (fix race condition)
        $this->ajax_model->set_call_status($slug, 'processing');
        
        switch ($form_call_method) {
            case 'form_call_skype':
                $phone_number = $phone_number.'@skype.com';
                break;
            case 'form_call_google':
                $phone_number = $phone_number.'@gmail.com';
                break;
            default:
                $phone_number = $phone_number;
                break;
        }
        
        #Pass local vars        
        $this->API_NUMBERS = $phone_number;
        $this->API_ORGANIZER = $phone_number;
        
        #call the API
        $respArr = $this->speek->callNow(
          $this->API_KEY,
          $this->API_FORMAT,
          $this->API_DESCRIPTION,
          $this->API_NUMBERS,
          $this->API_ORGANIZER,
          $this->API_MUSIC_ON_HOLDu,
          $this->API_GREETING,
          $this->API_GREETING_METHOD,
          $this->API_GREETING_TEXT,
          $this->API_GREETING_LINK,
          $this->API_RECORDING,
          $this->API_SOUND_ON_INOUT,
          $this->API_EXIT_ON_LEAVE,
          $this->API_CALL_NAME,
          $this->API_RSVP
          );
        
        #print the responce
        $response = json_decode($respArr);
        
        $call_id = $response->results->call_id;
        
        #prep data for history
        $data['call_id']            = $response->results->call_id;
        $data['method_identifier']  = $phone_number;
        switch ($form_call_method) {
            case 'form_call_skype':
                $data['method'] = 'Skype';
                break;
            case 'form_call_google':
                $data['method'] = 'Gmail';
                break;
            default:
                $data['method'] = 'Telephone';
                break;
        }
        
        if($this->ajax_model->add_call_id($slug, $call_id) == TRUE){
                
            #Change status to active (fix race condition)
            $this->ajax_model->set_call_status($slug, 'active');
                
            return $respArr;
           
        }
    }

	public function add_caller_id()
	{
		$slug		= $this->input->get('slug');	
		$call_id	= $this->input->get('caller_id');
	
		if($this->ajax_model->add_call_id($slug, $call_id) == TRUE){
		    $data['message'] = TRUE;
            $data['error']   = 'caller id added';
		}else{
		    $data['message'] = FALSE;
            $data['error']   = 'did not insert caller id';
        }
        echo json_encode($data);
        exit;
	}


	public function add_caller()
	{
        $form_name        = $this->input->get('form_name');
        $form_email       = $this->input->get('form_email');
		$form_call_method = $this->input->get('form_call_method');
		$phone_number     = $this->input->get('form_call_phone_number');
		$slug			  = $this->input->get('slug');
		$call_id          = $this->input->get('form_call_id');
        
        #Change status to processing (fix race condition)
        $this->ajax_model->set_call_status($slug, 'processing');
        
		switch ($form_call_method) {
			case 'form_call_skype':
				$phone_number = $phone_number.'@skype.com';
				break;
			case 'form_call_google':
				$phone_number = $phone_number.'@gmail.com';
				break;
			default:
				$phone_number = $phone_number;
				break;
		}
		
		#Pass local vars
		$this->API_CALL_ID = $call_id;
		$this->API_NUMBERS = $phone_number;
        
		#call the API 
		$respArr  = $this->speek->addParticipant(
		  $this->API_KEY,
		  $this->API_FORMAT,
		  $this->API_NUMBERS,
		  $this->API_CALL_ID,
		  $this->API_RETRY,
		  $this->API_HUNT,
		  $this->API_LONGITUDE,
		  $this->API_LATITUDE
          );
        
        $response = json_decode($respArr);
        
        if($response->status->ok == 1)
        {
            #prep data for history
            $data['full_name']         = $form_name;
            $data['email']             = $form_email;
            $data['call_id']           = $call_id;
            $data['method_identifier'] = $phone_number;
            switch ($form_call_method) {
                case 'form_call_skype':
                    $data['method'] = 'Skype';
                    break;
                case 'form_call_google':
                    $data['method'] = 'Gmail';
                    break;
                default:
                    $data['method'] = 'Telephone';
                    break;
            }
            
           if($this->ajax_model->add_caller($data) == TRUE){
               
                #Change status to active (fix race condition)
                $this->ajax_model->set_call_status($slug, 'active');
                
                #print the responce
                print_r($respArr);
                exit;
           }

       }
       else
       {
            #Change status to active (fix race condition)
            $this->ajax_model->set_call_status($slug, 'active');
            
            print_r($this->renew_caller($form_call_method, $phone_number, $slug));
       }

	}

    public function delete_slug()
    {
        $slug   = $this->input->get('slug');
        $gfc_id = $this->input->get('gfc_id');
        
        if($this->ajax_model->delete_slug($slug, $gfc_id) == TRUE)
        {
            echo json_encode(array('deleted'=>TRUE));
        }
        else
        {
            echo json_encode(array('deleted'=>FALSE));
        }
        
    }
   
    public function send_email()
    {
        $email   = trim($this->input->get('email_address'));
        $message = trim($this->input->get('email_message'));
        
        if (valid_email($email))
        {
            if(send_email($email, 'Speek.com Conference', $message))
            {
                $data['message'] = 'Message Sent';
                $data['sent']    = TRUE;
            }
            else
            {
                $data['message'] = 'Message not sent';
                $data['sent']    = FALSE;
            }
        }
        else
        {
            $data['message'] = 'Invalid Email Address';
            $data['sent']    = FALSE;
        }

        echo json_encode($data);
        
    }
    
    public function send_feedback()
    {
        $slug      = $this->input->get('slug');
        $full_name = $this->input->get('feedback_full_name');
        $email     = $this->input->get('feedback_email_address');
        $message   = $this->input->get('feedback_message');
        
        if($full_name == '')
        {
            $data['sent']  = FALSE;
            $data['error'] = 'Enter your Full Name';
        }
        elseif($email == '')
        {
            $data['sent']  = FALSE;
            $data['error'] = 'Enter your Email address';
        }
        elseif(!valid_email($email))
        {
            $data['sent']  = FALSE;
            $data['error'] = 'Enter a valid Email address';
        }
        elseif($message == '')
        {
            $data['sent']  = FALSE;
            $data['error'] = 'Enter your message';
        }
        else
        {
            $email_message = "Email: ".$email."/n".$message;
            
            if(send_email('joseph@luzquinos.net', 'Feedback from slug: '.$slug, $email_message))
            {
                $data['sent']  = TRUE;
                $data['error'] = 'Email was sent';
            }
            else
            {
                $data['sent']  = FALSE;
                $data['error'] = 'Error sending email.';
            }
        }
        
        echo json_encode($data);
        
    }
    
    /**
     * These methods were created to test the API, They can be removed.
     */
    public function test_callnow($phone_number)
    {
        #pass local vars        
        $this->API_NUMBERS = $phone_number;
        $this->API_ORGANIZER = $phone_number;
        
        #call the API
        $respArr = $this->speek->callNow(
          $this->API_KEY,
          $this->API_FORMAT,
          $this->API_DESCRIPTION,
          $this->API_NUMBERS,
          $this->API_ORGANIZER,
          $this->API_MUSIC_ON_HOLD,
          $this->API_GREETING,
          $this->API_GREETING_METHOD,
          $this->API_GREETING_TEXT,
          $this->API_GREETING_LINK,
          $this->API_RECORDING,
          $this->API_SOUND_ON_INOUT,
          $this->API_EXIT_ON_LEAVE,
          $this->API_CALL_NAME,
          $this->API_RSVP
          );
        
        #decode the responce
        $response = json_decode($respArr);
        
        pr($response);
        exit;
    }
    
    public function test_add_caller($call_id, $phone_number)
    {
        #Pass local vars
        $this->API_CALL_ID = $call_id;
        $this->API_NUMBERS = $phone_number;
        
        #call the API 
        $respArr  = $this->speek->addParticipant(
          $this->API_KEY,
          $this->API_FORMAT,
          $this->API_NUMBERS,
          $this->API_CALL_ID,
          $this->API_RETRY,
          $this->API_HUNT,
          $this->API_LONGITUDE,
          $this->API_LATITUDE
          );
        
        $response = json_decode($respArr);
        pr($response);
        exit;
    }
    
}
/* End of file ajax.php */