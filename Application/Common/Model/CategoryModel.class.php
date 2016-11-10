<?php
namespace Common\Model;

use Org\Bjy\Data;

/**
 * 分类model
 * @method CategoryModel getInstance() static
 */
class CategoryModel extends BaseModel
{
    // 自动验证
    protected $_validate = array(
        array('cname', 'require', '分类名不能为空'),
        array('sort', 'number', '排序必须为数字'),
    );

    /**
     * 添加数据
     * @return bool
     */
    public function addData()
    {
        $data = I('post.');
        if ($this->create($data)) {
            return $this->add();
        }
        return false;
    }

    /**
     * 修改数据
     * @return bool
     */
    public function editData()
    {
        $data = I('post.');
        if ($this->create($data)) {
            return $this->where(array('cid' => $data['cid']))->save($data);
        }
        return false;
    }

    // 删除数据
    public function deleteData($cid = null)
    {
        $cid = is_null($cid) ? I('get.cid') : $cid;
        $child = $this->getChildData($cid);
        if (!empty($child)) {
            $this->error = '请先删除子分类';
            return false;
        }
        $articleData = ArticleModel::getInstance()->getDataByCid($cid);
        if (!empty($articleData)) {
            $this->error = '请先删除此分类下的文章';
            return false;
        }
        if ($this->where(array('cid' => $cid))->delete()) {
            return true;
        } else {
            return false;
        }
    }

    //传递数据库字段名 获取对应的数据
    //不传递获取全部数据
    public function getAllData($field = 'all', $tree = true)
    {
        if ($field == 'all') {
            $data = $this->order('sort')->select();
            if ($tree) {
                return Data::tree($data, 'cname');
            } else {
                return $data;
            }

        } else {
            return $this->getField("cid,$field");
        }
    }

    //传递cid和field获取对应的数据
    public function getDataByCid($cid, $field = 'all')
    {
        if ($field == 'all') {
            return $this->where(array('cid' => $cid))->find();
        } else {
            return $this->where(array('cid' => $cid))->getField($field);
        }

    }

    // 传递cid获得所有子栏目
    public function getChildData($cid)
    {
        $childs = [];
        $data = $this->getAllData('all', false);
        $child = Data::channelList($data, $cid);
        foreach ($child as $k => $v) {
            $childs[] = $v['cid'];
        }
        return $childs;
    }

}
