<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionQrCodeConfigDb extends CI_Model{
    private $tableName = 't_distribution_qrcode_config';

    //添加
    public function add($data){
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    //获取分销二维码配置
    public function getQrcodeConfig($configId){
        $this->db->where('configId',$configId);
        return $this->db->get($this->tableName)->row_array();
    }
}
