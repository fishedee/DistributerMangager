<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Distribution extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->library('argv', 'argv');
    }

    /**
     * @view json
     */
    public function search(){
        //检查参数
        $dataWhere = $this->argv->checkGet(array(
            array('direction', 'require')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();

        if($dataWhere['direction'] == 'up')
            $where = array(
                'upUserId'=>$user['userId']
            );
        else if($dataWhere['direction'] == 'down')
            $where = array(
                'downUserId'=>$user['userId']
            );
        return $this->distributionAo->search($where, $dataLimit);
    }

    /**
     * @view json
     */
    public function get(){
        //检查参数
        $dataWhere = $this->argv->checkGet(array(
            array('distributionId', 'require')
        ));
        
        //检查权限
        $user = $this->loginAo->checkeMustLogin();

        return $this->distributionAo->get($user['userId'], $distributionId);
    }

    /**
     * @view json
     */
    public function request(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('upUserId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();

        $this->distributionAo->add($data['upUserId'], $user['userId'], 1);
    } 

    /**
     * @view json
     */
    public function accept(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('downUserId', 'require')
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustLogin();
        
        $where = array(
            'upUserId'=>$user['userId'],
            'downUserId'=>$data['downUserId']
        );

        $response = $this->distributionAo->search($where, array());
        if($response['count'] == 0)
            throw new CI_MyException(1, "没有相应的分成关系请求");
        $distribution = $response['data'][0];
        $this->distributionAo->mod($distribution['distributionId'], 2);
    }

    /**
     * @view json
     */
    public function del(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();
        $userId = $user['userId'];

        $this->distributionAo->del($userId, $data['distributionId']);
    }
}
