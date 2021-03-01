<?php
/**
 * Created by PhpStorm.
 * User: Reck
 * Date: 2021/3/1
 * Time: 10:20
 * Note: user.php
 */

namespace app\lib\model;


use app\lib\model;

class user extends model
{

    public function get_list()
    {
        return $this->select('a.user_id,b.nick_name')->from('user a')->join('user_info b', 'left')->on(['a.user_id', 'b.user_id'])->order(['a.user_id' => 'DESC'])->limit(0, 10)->getAll();
    }

}