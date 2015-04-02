<?php
	header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
	header("Last-Modified:".gmdate("D, d M Y H:i:s ")."GMT");
	header("Cache-control:no-cache,no-store,must-revalidate"); 
	header("Pragma:no-cache");
	if( $data instanceof Exception){
		$result = array(
			'code'=>$data->getCode(),
			'msg'=>$data->getMessage(),
			'data'=>$data->getData(),
		);
	}else{
		$result = array(
			'code'=>0,
			'msg'=>'',
			'data'=>$data
		);
	}
	$output = json_encode($result);
	if( $output == null ){
		$output = json_encode(array(
			'code'=>1,
			'msg'=>'输出中含有非UTF8编码',
			'data'=>''
		));
		log_message('ERROR','输出中含有非UTF8编码');
	}else{
		echo $output;
	}
?>
