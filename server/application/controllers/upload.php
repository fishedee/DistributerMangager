<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->library('argv','argv');
		$this->load->library('image','','image');
		$this->load->library('fileUpload','','fileUpload');
		$this->load->library('uedit','','uedit');
		$this->load->helper('file');
    }
	
	/**
	* @view json
	*/
	private function getAllFiles($address){
		if( strpos($address,'/data/upload/') != 0 )
			throw new CI_MyException(1,'只能查看上传文件夹');
	
		$folderAddress = dirname(__FILE__).'/../../../'.$address;
		$folderPrefixLength = strlen(dirname(__FILE__).'/../../../');
		$folderInfo = get_dir_file_info($folderAddress,false);
		$result = array();
		$result[] = $address;
		foreach( $folderInfo as $fileName=>$fileInfo ){
			$result[] = substr(
				$fileInfo['server_path'],
				strpos($fileInfo['server_path'],$address)
			);
		}
		return $result;
	}
	
	/**
	* @view json
	*/
	public function image()
	{
		//return $this->fileUpload->simpleImage('data');
		$upload_path = $this->config->item('upload_path').'/image/'.date("Y").date("m").date('d');
		$option = array(
			'upload_path'=>$upload_path,
			'max_size'=>$this->config->item('upload_max_size'),
		);
		$option['field'] = 'data';
		//print_r(dirname($option['upload_path']));die;
		if (!file_exists($option['upload_path']))exec('mkdir -p '.$option['upload_path']);
		return $this->config->item('upload_url').'image/'.date("Y").date("m").date('d').'/'.$this->fileUpload->image($option)['file_name'];

	}

	/**
	* @view json
	*/
	public function cert()
	{
//		return $this->fileUpload->simpleFile('data','pem');

		$userId = $this->session->userdata('userId');
		$option = array(
			'upload_path'=>$this->config->item('upload_path').'/pem/'.$userId.'/',
			'max_size'=>$this->config->item('upload_max_size'),
		);
		$option['field'] = 'data';
		$option['allowed_types'] = 'pem';
		//print_r($option);die;
		if (!file_exists($option['upload_path']))exec('mkdir -p '.$option['upload_path']);
		$result = $this->fileUpload->file($option);
			
		return $this->config->item('upload_url').'pem/'.$userId.'/'.$result['file_name'];
	}
	
	/**
	* @view json
	*/
	public function compressFile()
	{
		$fileName = $this->fileUpload->simpleFile('data','zip');
		$folderName = uniqid();
		$fileAddress = dirname(__FILE__).'/../../../'.$fileName;
		$folderAddress = dirname(__FILE__).'/../../../data/upload/template/'.$folderName;
		$cmd = 'unzip -o '.$fileAddress.' -d '.$folderAddress;
		shell_exec($cmd);
		@unlink($fileAddress);
		$folderAddress = '/data/upload/template/'.$folderName;
		return $this->getAllFiles($folderAddress);;
	}
	
	/**
	* @view
	*/
	public function ueditor()
	{
		//return $this->uedit->control();
		echo $this->uedit->control();
	}	
	
	/**
	* @view json
	*/
	public function readdir()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('address','require')
		));
		$address = $data['address'];
		return $this->getAllFiles($address);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
