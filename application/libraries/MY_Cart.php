<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Cart extends CI_Cart {

    public function __construct()
    {
        parent::__construct();
    }
	public function rowid($id, $options){
		$rowid = '';
		if (isset($options) && count($options) > 0)
		{
			$rowid = md5($id.serialize($options));
		}
		else
		{
			// No options were submitted so we simply MD5 the product ID.
			// Technically, we don't need to MD5 the ID in this case, but it makes
			// sense to standardize the format of array indexes for both conditions
			$rowid = md5($id);
		}
		return $rowid;
	}
}