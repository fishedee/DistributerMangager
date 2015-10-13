<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Cooperation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('cooperation/cooperationDb','cooperation');
			$this->load->library('argv', 'argv');
		}

		public function add(){
			if($this->input->is_ajax_request()){
				$data = $this->input->post();
				foreach ($data as $key => $value) {
					if($value == ''){
						echo '提交资料不齐全';
						die;
					}
				}
				$userId = $data['userId'];
				unset($data['userId']);
				$result = $this->cooperation->add($userId,$data);
				if($result){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		/**
		 * @view json
		 */
		public function search(){
			if($this->input->is_ajax_request()){

				$dataWhere = $this->argv->checkGet(array(
					array('type','option'),
					array('business_name','option'),
					array('province','option'),
					array('city','option'),
				));
				$userId    = $this->session->userdata('userId');
		        $dataLimit = $this->argv->checkGet(array(
					array('pageIndex','require'),
					array('pageSize','require'),
				));
				$dataWhere['userId'] = $userId;
				$cooperationInfo_data =  $this->cooperation->search($dataWhere,$dataLimit);
				return $cooperationInfo_data;
			}
		}

		//测试excel
		public function excel(){
			require_once(BASEPATH.'/libraries/PHPExcel.php');
			require_once(BASEPATH.'/libraries/PHPExcel/IOFactory.php');

			$objPHPExcel = new PHPExcel();

			$objPHPExcel->getProperties()
			->setCreator("zzh")
			->setLastModifiedBy("zzh")
			->setTitle("数据EXCEL导出")
		    ->setSubject("数据EXCEL导出")
		    ->setDescription("备份数据")
		    ->setKeywords("excel")
		    ->setCategory("result file");

		    $cooperationInfo = $this->cooperation->searchAll();

		    //设置表头信息
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','加盟类型')->setCellValue('B1','企业名称')->setCellValue('C1','用户名称')->setCellValue('D1','联系电话')->setCellValue('E1','省份')->setCellValue('F1','城市')->setCellValue('G1','地址')->setCellValue('H1','意向');
		    $objPHPExcel->getActiveSheet(0)->getColumnDimension('H')->setWidth(30);

		    foreach ($cooperationInfo as $key => $value) {
		    	$num = $key + 2;
		    	// $data = json_decode($value['cooperationData']);
		    	$objPHPExcel->setActiveSheetIndex(0)
				//Excel的第A列，uid是你查出数组的键值，下面以此类推
				->setCellValue('A'.$num, ' '.$value['type'])
				->setCellValue('B'.$num, ' '.$value['business_name'])
				->setCellValue('C'.$num, $value['user_name'])
				->setCellValue('D'.$num, $value['contract'])
				->setCellValue('E'.$num, $value['province'])
				->setCellValue('F'.$num, $value['city'])
				->setCellValue('G'.$num, $value['newlocation'])
				->setCellValue('H'.$num, $value['will']);
		    }
		    $objPHPExcel->getActiveSheet()->setTitle('加盟');
  			$objPHPExcel->setActiveSheetIndex(0);
  			$name = '招商加盟';
  			ob_end_clean();
  			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}

		//测试当前页面
		public function currentPage(){
			// $pageSize = $_GET['pageSize'];
			// $current  = $_GET['current'];
			// $type     = $_GET['type'];
			// $business_name = $_GET['business_name'];
			// $province   = $_GET['province'];
			// $city       = $_GET['city'];

			// $this->load->helper('url');
			// $pageSize = $this->uri->segment(6);
			// $current  = $this->uri->segment(4);
			// $type  = $this->uri->segment(8);
			// $business_name  = $this->uri->segment(10);
			// $province  = $this->uri->segment(12);
			// $city  = $this->uri->segment(14);
			$pageSize = $this->input->get('pageSize');
			$current  = $this->input->get('current');
			$type     = $this->input->get('type') ? $this->input->get('type') : NULL;
			$business_name = $this->input->get('business_name') ? $this->input->get('business_name') : NULL;
			$province = $this->input->get('province') ? $this->input->get('province') : NULL;
			$city     = $this->input->get('city') ? $this->input->get('city') : NULL;
			$pageSize = $this->input->get('pageSize') ? $this->input->get('pageSize') : NULL;
			$current  = $this->input->get('current') ? $this->input->get('current') : NUll;

			$dataLimit['pageIndex'] = ($current - 1) * $pageSize;
			$dataLimit['pageSize']  = $pageSize;
			$dataWhere['userId']    = $this->session->userdata('userId');
			$dataWhere['type']      = $type;
			$dataWhere['business_name'] = $business_name;
			$dataWhere['province']   = $province;
			$dataWhere['city']       = $city;
			$cooperationInfo = $this->cooperation->search($dataWhere,$dataLimit);
			$cooperationInfo = $cooperationInfo['data'];
			require_once(BASEPATH.'/libraries/PHPExcel.php');
			require_once(BASEPATH.'/libraries/PHPExcel/IOFactory.php');

			$objPHPExcel = new PHPExcel();

			$objPHPExcel->getProperties()
			->setCreator("zzh")
			->setLastModifiedBy("zzh")
			->setTitle("数据EXCEL导出")
		    ->setSubject("数据EXCEL导出")
		    ->setDescription("备份数据")
		    ->setKeywords("excel")
		    ->setCategory("result file");

		    //设置表头信息
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','加盟类型')->setCellValue('B1','企业名称')->setCellValue('C1','用户名称')->setCellValue('D1','联系电话')->setCellValue('E1','省份')->setCellValue('F1','城市')->setCellValue('G1','地址')->setCellValue('H1','意向');
		    $objPHPExcel->getActiveSheet(0)->getColumnDimension('H')->setWidth(30);
		    foreach ($cooperationInfo as $key => $value) {
		    	$num = $key + 2;
		    	// $data = json_decode($value['cooperationData']);
		    	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$num, ' '.$value['type'])
				->setCellValue('B'.$num, ' '.$value['business_name'])
				->setCellValue('C'.$num, $value['user_name'])
				->setCellValue('D'.$num, $value['contract'])
				->setCellValue('E'.$num, $value['province'])
				->setCellValue('F'.$num, $value['city'])
				->setCellValue('G'.$num, $value['newlocation'])
				->setCellValue('H'.$num, $value['will']);
		    }
		    $objPHPExcel->getActiveSheet()->setTitle('加盟');
  			$objPHPExcel->setActiveSheetIndex(0);
  			$name = '招商加盟';
  			ob_end_clean();
  			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}

		/**
		 * @view json
		 */
		public function getUrl(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				$url = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/league.html';
				return $url;
			}
		}
	}
 ?>