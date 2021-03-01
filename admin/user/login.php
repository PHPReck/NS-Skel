<?php
/**
 * Created by PhpStorm.
 * User: Reck
 * Date: 2021/2/26
 * Time: 11:19
 * Note: login.php
 */

namespace admin\user;


use app\lib\base;
use app\lib\model\sys_config;
use app\lib\model\user;
use Core\Lib\IOUnit;

class login extends base
{

    public function acc()
    {
        $this->redis->set('2', 2);

        var_dump($this->redis->get('2'));
        var_dump(IOUnit::new()->src_cmd);


        var_dump(IOUnit::new()->src_input);

        $result = sys_config::new()->get_info('config_val', ['config_id', 1]);
        var_dump($result);

        $result = user::new()->get_list();
        //var_dump($result);
        $this->error->set(12, 0, '返回成功');
        return $result;

        $this->fail(12, '222');

    }

}