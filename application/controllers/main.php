<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

/**
 * 
 */
class Main extends CI_Controller {

	function __Construct()
	{
		parent::__Construct();
        
        $this->load->model('ajax_model');
		$this->load->helper('recaptchalib');
        $this->load->library('user_agent'); 
	}
	
    private function displaySpecialIPhoneView() 
	{ 
	     $no_app = $this->input->get('no_app'); 
	     return ($this->agent->is_mobile('iphone') && $no_app==NULL);
    }

	public function index()
	{
		$slug = $this->uri->segment(1);
		if($slug == '')
		{
			$publickey = $this->config->item('recaptcha_public_key');
			$data['recaptcha_html'] = recaptcha_get_html($publickey);
			$this->load->view('main/create_view', $data);
		}
		elseif ($conference = $this->ajax_model->get_slug_info($slug))
		{
			if($this->displaySpecialIPhoneView())
			{    
                $data['conference'] = $conference; 
		        $this->load->view('main/slug_view_check_iPhoneApp'); 
            }
            else
            { 	
			   $data['conference'] = $conference;
			   $this->load->view('main/slug_view',$data);
            } 
		}
		else
		{
			$this->output->set_status_header('404');
		}	
	}	

}

/* End of file main.php */
