<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
use Common\Model\ArticleModel;
use Common\Model\ChatModel;

/**
 * 后台首页
 */
class IndexController extends AdminBaseController
{
    // 后台首页
    public function index()
    {
        $this->display();
    }

    // 欢迎页面
    public function welcome()
    {
        $assign = array(
            'all_article' => ArticleModel::getInstance()->getCountData(),
            'delete_article' => ArticleModel::getInstance()->getCountData(array('is_delete' => 1)),
            'hide_article' => ArticleModel::getInstance()->getCountData(array('is_show' => 0)),
            'all_chat' => ChatModel::getInstance()->getCountData(),
            'delete_chat' => ChatModel::getInstance()->getCountData(array('is_delete' => 1)),
            'hide_chat' => ChatModel::getInstance()->getCountData(array('is_show' => 0)),
            'all_comment' => M('Comment')->count(),
        );
        $this->assign($assign);
        $this->display();
    }

}

