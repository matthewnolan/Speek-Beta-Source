<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

    /**
     * Checks for duplicate slugs
     * @param   : string
     * @return  : boolean
     */
	function check_duplicate($slug) {

		$query = $this -> db -> get_where('conference', array('slug' => $slug));
        
        return ($query -> num_rows() > 0 ? FALSE : TRUE);
           
	}

    /**
     * Checks if expired by comparing to current time
     * @param   : string
     * @return  : boolean
     */
    function is_expired($slug)
    {
        
        $query = $this->db->get_where('conference', array('slug' => $slug));
        $row = $query->row();
        
        $expires_at = strtotime($row->expires_at);
        $time_now   = time();
        
        return ($time_now > $expires_at ? TRUE : FALSE);

    }

    /**
     * Creates a slug entry in the DB
     * @param   : string
     * @return  : boolean
     */
	function create_slug($slug) {
		$data = array(
			'slug' 		 => $slug,
			'created_at' => date('Y-m-d H:i:s'),
			'expires_at' => date("Y-m-d H:i:s",time() + 86400), // cureent time + 24 hours 
			'ip_address' => $_SERVER['REMOTE_ADDR']
		);
        
        return ($this->db->insert('conference', $data) ? TRUE : FALSE);
	}
    
    /**
     * Adds gfc_id to slug
     * @param   : string
     * @return  : boolean
     */
    public function add_gfc_id($slug, $gfc_id)
    {
        $this->db->where('slug',$slug);
        return ($this->db->update('conference', array('gfc_id' => $gfc_id))? TRUE : FALSE);
    }
	
    /**
     * Checks if there is a call_id in the slug
     * @param   : string
     * @return  : boolean
     */
	public function first_call_check($slug)
	{
		$query = $this->db->get_where('conference', array('slug'=>$slug));
		$conference = $query->row();
        
        return ($conference->call_id == NULL ? FALSE : TRUE);

	}
	
    /**
     * Adds call_id entry to the slug
     * @param   : string
     * @return  : boolean
     */
	public function add_call_id($slug, $call_id)
	{	
		$this->db->where('slug',$slug);
		if($this->db->update('conference', array('call_id' => $call_id)))
		{
			return TRUE;
		}else{
			return FALSE;
		}
	}
    
    /**
     * Gets an object with the slug info
     * @param   : string
     * @return  : object
     */
    public function get_slug_info($slug)
    {
        $query = $this->db->get_where('conference', array('slug' => $slug));      
        return $query->row();
    }

    /**
     * Gets an obget with slug info for the current user
     * @param   : string
     * @return  : object
     */
    public function get_current_callers($slug, $gfc_id)
    {
        $query = $this->db->get_where('conference', array('slug' => $slug, 'gfc_id'=>$gfc_id));      
        return $query->row();
    }
    
    /**
    * Gets slugs for current user
    * @param   : string
    * @return  : object
    */
    public function get_previous_slugs($gfc_id)
    {
        $query = $this->db->get_where('conference', array('gfc_id' => $gfc_id));      
        return $query->result();
    }
    
    /**
    * Sets call status to fix race issue
    * @param   : string
    * @return  : boolean
    */
    public function set_call_status($slug, $status)
    {
        $data = array('call_status'=>$status);
        $this->db->where('slug', $slug);
        return ($this->db->update('conference', $data)? TRUE : FALSE);
    }
    
    /**
    * Deletes Slug
    * @param   : string
    * @return  : boolean
    */
    public function delete_slug($slug, $gfc_id)
    {
        return ($this->db->delete('conference', array('slug' => $slug, 'gfc_id' => $gfc_id)) ? TRUE : FALSE);
    }
    
    /**
     * Gets a list of the current callers
     * @param   : number
     * @return  : object
     */
    public function current_callers($call_id)
    {
        $query = $this->db->get_where('history', array('call_id'=>$call_id));
        
        return $query->result();
    }
    
    /**
     * Adds new caller entry to history table
     * @param   : array
     * @return  : boolean
     */
    public function add_caller($args = array())
    {
        $data['full_name']         = $args['full_name'];
        $data['email']             = $args['email'];
        $data['call_id']           = $args['call_id'];
        $data['ip_address']        = $_SERVER['REMOTE_ADDR'];
        $data['method']            = $args['method'];
        $data['method_identifier'] = $args['method_identifier'];
        $data['created_at']        = date('Y-m-d H:i:s');
        $data['updated_at']        = date('Y-m-d H:i:s');
        
        return ($this->db->insert('history', $data) ? TRUE : FALSE);
    }

}
/* End of file ajax_model.php */