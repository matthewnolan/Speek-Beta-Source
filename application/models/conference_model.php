<?php

class Conference_model extends CI_Model {
	
  public $id = '';
  public $call_id = '';
  public $gfc_id = '';
  public $slug = '';
  public $ip_address = '';
  public $expires_at = '';
  public $created_at = '';
  public $updated_at = '';
  public static $History = '';
  public static $isNew = true;


  public function __Construct()
  {
	parent::__construct();
  }
  public function findOneById($id)
  {
	$query = $this->db->get_where('conference', array('id' => $id));
	$result = $query->row_array();
	if (empty($result))
	{
		return false;
	}

	$this->populateThisObject($result);

	return $this;
	
  }
  public function findOneByCallId($id)
  {
	$query = $this->db->get_where('conference', array('call_id' => $id));
	$results = $query->row_array();
	if (empty($results))
	{
		return false;
	}
	
	$this->populateThisObject($result);
	
	return $this;
  }
  
  public function isAvailable($slug)
  {	
	$query = $this->db->get_where('conference', array('slug' => $slug));
	$row   = $query->row();

    if ($query->num_rows() > 0)
    {
        if((strtotime($row->expires_at) > time()) || $row->expires_at == NULL)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }   
    }
    else
    {
        return TRUE;
    }

	/*
     * 
       $results = $query->result_array();
	// function returns true if no match for slug is found.
		if (empty($results))
		{
			return true;
		}
		
		foreach ($results as $conference)
		{
			// function returns false if any result has yet to expire OR if expiration date is null.
			if(strtotime($conference['expires_at']) > time() || $conference['expires_at'] == null)
			{
				return false;
			}
		}
		
		return true;*/
	
  }
  public function isExpired($slug)
  {
	$query = $this->db->get_where('conference', array('slug' => $slug));
	$results = $query->result_array();
	foreach ($results as $conference)
	{
		// conference does not expire
		if($conference['expires_at'] == null)
		{
			return false;
		}
		elseif (strtotime($conference['expires_at']) > time())
		{
			return false;
		}
	}
	return true;
  }
  public function findOneBySlug($slug)
  {
	$ci = get_instance();
	$ci->db->where('slug', $slug);
	$ci->db->limit(1);
	$ci->db->order_by("id", "desc"); 
	
	$query = $ci->db->get('conference');
	$result = $query->row_array();	
	
	if (empty($result))
	{
		return false;
	}

	$this->populateThisObject($result);
	
	return $this;	
	
  }
  public function getId()
  {
	return $this->id;
  }
  public function getSlug()
  {
	return $this->slug;
  }
  public function getCallId()
  {
 	return $this->call_id;
  }
  public function getExpiresAt()
  {
	return $this->expires_at;
  }
  public function getIpAddress($ip)
  {
	return $this->ip_address;
  }
  public function getHistory()
  {
	$query = $this->db->get_where('history', array('call_id' => $this->call_id));

		return $query;
  
  } 
  public function setId($id)
  {
		$this->id = $id;
  }
  public function setSlug($slug)
  {
	$this->slug = $slug;
  }
  public function setCallId($id)
  {
	$this->call_id = $id;
  }
  public function getGfcId()
  {
	return $this->gfc_id;
  }
  public function setGfcId($gfc_id)
  {
	$this->gfc_id = $gfc_id;
  }
  public function setIpAddress($ip)
  {
	$this->ip_address = $ip;
  }
  public function setHistory($history)
  {
	self::$History = $history;
  }
  public function setExpiresAt($expires_at = '')
  {
	$this->expires_at = $expires_at;
  }
  public function save()
  {
	$this->updated_at = date('Y-m-d H:i:s');
	
	if (!$this->isNew())
	{
		$query = $this->db->where('id', $this->getId());
		$query = $this->db->update('conference', get_object_vars($this));	
	}
	else
	{
		$this->created_at = date('Y-m-d H:i:s');
		$this->db->insert('conference', get_object_vars($this));
		
		// getting & returning ID
		$conference = $this->findOneBySlug($this->getSlug());
		$this->setId($conference->getId());
		
		self::$isNew == false;
	}
	return $this;
  }
  public function isNew()
  {
	return self::$isNew;
  }
  protected function populateThisObject($conference)
  {
		$this->id = $conference['id'];
		$this->call_id = $conference['call_id'];
		$this->slug = $conference['slug'];
		$this->ip_address = $conference['ip_address'];
		$this->updated_at = $conference['updated_at'];
		$this->created_at = $conference['created_at'];
		$this->gfc_id = $conference['gfc_id'];
		self::$History = $this->getHistory();
		self::$isNew = false;
  }
}
