<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->library('argv','argv');
		$this->load->library('image','','image');
		$this->load->library('fileUpload','','fileUpload');
		$this->load->library('uedit','','uedit');
    }
	
	/**
	* @view json
	*/
	public function image()
	{
		return $this->fileUpload->simpleImage('data');
	}
	
	/**
	* @view
	*/
	public function ueditor()
	{
		return $this->uedit->control();
	}	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
