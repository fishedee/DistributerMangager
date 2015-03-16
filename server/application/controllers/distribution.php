<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Distribution extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->model('distribution/distributionStateEnum', 'distributionStateEnum');
        $this->load->library('argv', 'argv');
    }

    /**
     * @view json
     */
    public function getAllState(){
        return $this->distributionStateEnum->names;
    }

    /**
     * @view json
     */
    public function search(){
        //检查参数
        $dataWhere = $this->argv->checkGet(array(
            array('direction', 'require'),
            array('state', 'option')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();

        if($dataWhere['direction'] == 'down')
            $where = array(
                'upUserId'=>$user['userId']
            );
        else 
            $where = array(
                'downUserId'=>$user['userId']
            );
        if( isset($dataWhere['state']))
            $where['state'] = $dataWhere['state'];
        //var_dump($dataWhere);
        return $this->distributionAo->search($where, $dataLimit);
    }

    /**
     * @view json
     */
    public function get(){
        //检查参数
        $data = $this->argv->checkGet(array(
            array('distributionId', 'require')
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustLogin();

        return $this->distributionAo->get($user['userId'], $data['distributionId']);
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

        $this->distributionAo->request($data['upUserId'], $user['userId']);
    } 

    /**
     * @view json
     */
    public function accept(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require')
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustLogin();

        $this->distributionAo->accept($user['userId'],$data['distributionId']);
    }

    /**
     * @view json
     */
    public function mod(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require')
        ));
        $distributionId = $data['distributionId'];

        $data = $this->argv->checkPost(array(
            array('distributionPercentShow', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();

        $this->distributionAo->modPrecent(
            $user['userId'],
            $distributionId,
            $data['distributionPercentShow']
        );
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

    /**
     * @view json
     */
    public function getLink(){
        //检查参数
        $data = $this->argv->checkGet(array(
            array('upUserId', 'require'),
            array('downUserId', 'require')
        ));

        return $this->distributionAo->getLink($data['upUserId'], $data['downUserId']);
    }
}