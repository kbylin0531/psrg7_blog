<?php
namespace Common\Model;

use Org\Bjy\Page;

/**
 * 第三方登陆model
 * @method OauthUserModel getInstance() static
 * @method getFieldById(string $p1, string $p2)
 * @method array|int|string getFieldByOpenid($table, $fields)
 */
class OauthUserModel extends BaseModel
{
    // 自动验证
    protected $_validate = array(
        array('type', 'require', '类型必填'),
        array('nickname', 'require', '昵称必填'),
        array('head_img', 'require', '头像必填'),
        array('openid', 'require', 'openid必填'),
        array('access_token', 'require', 'access_token必填'),
    );

    // 自动完成
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('last_login_time', 'time', 3, 'function'),
        array('last_login_ip', 'getLoginIp', 3, 'callback'),
        array('login_times', 1),
        array('status', 1),
    );

    // 获得登陆ip
    public function getLoginIp()
    {
        $ip = get_client_ip();
        return $ip;
    }

    // 添加数据
    public function addData($data)
    {
        if ($create = $this->create($data)) {
            $id = $this->add($create);
            return $id;
        }
        return false;
    }

    /**
     * 修改数据
     * @param $data
     * @return array|bool|int|string
     */
    public function editData($data)
    {
        $openid = $data['openid'];
        if ($create = $this->create($data)) {
            $create['login_times'] = array('exp', 'login_times+1');
            unset($create['status']);
            unset($create['create_time']);
            $this->where(array('openid' => $openid))->save($create);
            return $this->getFieldByOpenid($openid, 'id');
        }
        return false;
    }

    // 传递openid获取单条数据
    public function getDataByOpenid($openid)
    {
        return $this->where(array('openid' => $openid))->find();
    }

    // 获取用户分页数据
    public function getPageData()
    {
        $count = $this->count();
        $page = new Page($count, 20);
        $list = $this
            ->order('last_login_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $type = array('', 'QQ', '新浪微博', '豆瓣', '人人', '开心网');
        foreach ($list as $k => $v) {
            $list[$k]['type'] = $type[$v['type']];
        }
        $data = array(
            'data' => $list,
            'page' => $page->show()
        );
        return $data;

    }


}
