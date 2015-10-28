<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dish extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('dish/dishTypeAo','dishTypeAo');
        $this->load->model('dish/dishAo','dishAo');
        $this->load->model('client/clientLoginAo','clientLoginAo');
        $this->load->library('argv','argv');
    }

    /**
     * @view json
     * 查询菜品
     */
    public function search(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('dishName', 'option'),
            array('dish_type_id','option'),
        ));
        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));
         //检查权限
        $user = $this->loginAo->checkOrder(
            $this->userPermissionEnum->ORDER_DINNER
        );
        $userId = $user['userId'];
        return $this->dishAo->search($userId,$dataWhere,$dataLimit);
    }

    /**
     * @view json
     * 增加菜品
     */
    public function add(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            return $this->dishAo->add($userId,$data);
        }
    }

    /**
     * @view json
     * 编辑商品信息
     */
    public function mod(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            $dishId = $this->input->post('dishId');
            return $this->dishAo->mod($userId,$dishId,$data);
        }
    }

    /**
     * @view json
     * 获取菜品信息
     */
    public function getDish(){
        // if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $dishId = $this->input->get('dishId');
            $mobile = $this->input->get('mobile') ? $this->input->get('mobile') : 0;
            return $this->dishAo->getDish($userId,$dishId,$mobile);
        // }
    }

    /**
     * @view json
     * 更改上下架状态
     */
    public function modState(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $dishId = $this->input->post('dishId');
            return $this->dishAo->modState($userId,$dishId);
        }
    }

    /**
     * @view json
     * 删除菜品
     */
    public function del(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $dishId = $this->input->post('dishId');
            return $this->dishAo->del($userId,$dishId);
        }
    }

    /**
     * @view json
     * 前端获取菜单
     */
    public function getMenu(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            // 检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            return $this->dishAo->getMenu($userId);
        }
    }

    /**
     * @view json
     * 获取菜品标题、缩略图
     */
    public function commentGetDish(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            // 检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $dishId = $this->input->get('dishId');
            return $this->dishAo->commentGetDish($userId,$dishId);
    	}
    }


    public function addTest(){
        // $data = $this->input->post('data');
        // $icon = $data['icon'];
        $icon = dirname(__FILE__).'/../../../data/upload/680e4eba06aed12116bb2bd1295559ac.jpg';
        var_dump(getimagesize($icon));
        // $this->load->library('image_lib');
        // $config['image_library'] = 'gd2';
        // $config['source_image'] = $icon;
        // $config['create_thumb'] = TRUE;
        // $config['maintain_ratio'] = TRUE;
        // $config['width']     = 75;
        // $config['height']   = 50;
        // $this->image_lib->initialize($config);
        // if($this->image_lib->resize()){
        //     // var_dump($this->image->lib->resize());
        //     echo 'success!';
        // }else{
        //     echo $this->image_lib->display_errors();
        // }
        // var_dump($this->image_lib->resize());
    }

	//Edward
	//
    /**
     * @view json
     * websockit验证密码
     * Edward
     */
	public function websockitPassword(){
	
//	$new=$_GET['userId'].'我是Edward'.$_SERVER['REMOTE_ADDR'];
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
	$password=password_hash($userId.'我是Edward'.$_SERVER['REMOTE_ADDR'] ,PASSWORD_BCRYPT);

//	echo( $new."\n" );
//	echo "</br>";
//	echo( $old."\n" );
//	echo "</br>";

//	print_r(password_verify($new,$old));

//if (password_verify($new,$old)) {
//    echo 'Password is valid!';
//} else {
//    echo 'Invalid password.';
//}

	//print_r($_SERVER['REMOTE_ADDR']);die;	

return array('userId'=>$userId,'password'=>$password,'ip'=>$_SERVER['SERVER_ADDR']);

	}

}
