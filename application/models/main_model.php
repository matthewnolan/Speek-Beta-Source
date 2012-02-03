<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

/**
 * Class for Main Controller
 */
class Main_model extends CI_Model {

	function __construct() {
		parent::__Construct();
	}

	/**
	 * Returns Call data from the database
	 * @param	String
	 * @return	Array
	 */
	public function conf($slug = '') {
		if ($slug != '') {

			$query = $this -> db -> get_where('conference', array('slug' => $slug));

			return $query -> row_array();
			exit ;

		} else {

			exit;

		}

	}

}
?>