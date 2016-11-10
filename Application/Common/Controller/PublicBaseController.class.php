<?php
namespace Common\Controller;
use Common\Model\CategoryModel;
use Common\Model\LinkModel;
use Common\Model\TagModel;

/**
 * 其他通用基类Controller
 */
class PublicBaseController extends BaseController{
    /**
     * 初始化方法
     */
    public function _initialize(){
        parent::_initialize();
        // 分配常用数据
        $assign=array(
            'categorys'=>CategoryModel::getInstance()->getAllData(),
            'tags'=>TagModel::getInstance()->getAllData(),
            'links'=>LinkModel::getInstance()->getDataByState(0,1),
            );
        $this->assign($assign);

    }




}

