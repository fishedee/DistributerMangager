<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Distribution extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->model('distribution/distributionStateEnum', 'distributionStateEnum');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
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
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

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
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        return $this->distributionAo->get($user['userId'], $data['distributionId']);
    }

    /**
     * @view json
     */
    public function getByLink(){
        //检查参数
        $data = $this->argv->checkGet(array(
            array('upUserId', 'require'),
            array('downUserId', 'require'),
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
        $userId = $user['userId'];

        return $this->distributionAo->getByLink($userId,$data['upUserId'], $data['downUserId']);
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
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

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
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION_PRO
        );

        if( $this->distributionAo->getAcceptLinkNum($user['userId']) >= $user['downDistributionNum'])
            throw new CI_MyException(1,'您的帐号只能最多可以设置 '.$user['downDistributionNum'].' 个分销商');

        $this->distributionAo->accept($user['userId'],$data['distributionId']);
    }

    /**
     * @view json
     */
    public function mod(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require'),
            array('shopUrl', 'require'),
        ));
        $distributionId = $data['distributionId'];
        $shopUrl = $data['shopUrl'];

        $data = $this->argv->checkPost(array(
            array('distributionPercentShow', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        $this->distributionAo->modPrecent(
            $user['userId'],
            $distributionId,
            $data['distributionPercentShow'],
            $shopUrl
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
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
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

    /**
     * @view json
     * 判断有无绑定
     */
    public function judgeBind(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            return $this->distributionAo->judgeBind($userId,$clientId);
        }
    }

    /**
     * @view json
     * 绑定
     */
    public function bind(){
        if($this->input->is_ajax_request()){
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            return $this->distributionAo->bind($userId,$clientId,$username,$password);
        }
    }

    /**
     * @view json
     * 解绑
     */
    public function unBind(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            return $this->distributionAo->unBind($userId,$clientId);
        }
    }

    /**
     * @view json
     * 手机端查询
     */
    public function mobileSearch(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            $data   = $this->input->post();   //ajax 通过post方式提交过来的数据
            return $this->distributionAo->mobileSearch($userId,$clientId,$data);
        }
    }

    /**
     * @view json
     * 手机端分销商申请
     */
    public function ask(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $data = $this->input->post();
            return $this->distributionAo->ask($userId,$data);
        }
    }
}
