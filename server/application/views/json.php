<?php
	$result = array(
		"code"=>$code,	
		"msg"=>$msg,
		"data"=>$data
	);
	$output = json_encode($result);
	if( $output == null ){
		$output = json_encode(array(
			'code'=>1,
			'msg'=>'输出中含有非UTF8编码',
			'data'=>''
		));
		log_message('ERROR','输出中含有非UTF8编码');
	}else{
		if( $result['code'] != 0 ){
			log_message('ERROR',$result['msg']);
		}
		
		echo $output;
	}
?>
