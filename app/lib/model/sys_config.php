<?php
/**
 * Created by PhpStorm.
 * User: Reck
 * Date: 2021/2/26
 * Time: 11:44
 * Note: sys_config.php
 */

namespace app\lib\model;


use app\lib\model;

class sys_config extends model
{

    public function get_cnt(array $where)
    {
        return $this->select('count(config_id)')->where($where)->getRow(\PDO::FETCH_COLUMN)[0];
    }


}