<?php
/**
 * Created by PhpStorm.
 * User: Reck
 * Date: 2021/3/8
 * Time: 10:19
 * Note: errno.php
 */

namespace app\lib;


use Core\Lib\IOUnit;
use Ext\libErrno;

class errno extends base
{
    /**
     * @var \Core\Factory|\Ext\libErrno $error
     */
    public $error;

    public function __construct()
    {
        parent::__construct();

        //加载错误码
        $this->error = libErrno::new('app/msg')->load('code');

        //默认操作成功，具体状态码在业务中修改
        $this->error->set(10010, 0);

    }


    /**
     * 失败返回
     *
     * @param int    $code
     * @param string $msg
     */
    public function fail(int $code, string $msg = '')
    {
        $this->error->set($code, 1, $msg);
        IOUnit::new()->output();
        exit(0);
    }

}