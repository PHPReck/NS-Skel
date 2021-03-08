<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2/6/2020
 * Time: 11:44 AM
 * Note: base.php
 */

namespace app\lib;

use Core\Factory;
use Core\Lib\App;
use Core\Lib\IOUnit;
use Ext\libCache;
use Ext\libConfGet;
use Ext\libCrypt;
use Ext\libErrno;
use Ext\libLock;
use Ext\libMySQL;
use Ext\libPDO;
use Ext\libQueue;
use Ext\libRedis;

/**
 * Class base
 *
 * @package app
 */
class base extends Factory
{
    /**
     * @var libMySQL $mysql
     */
    public $mysql;

    /**
     * @var \Redis $redis
     */
    public $redis;

    /**
     * @var \Ext\libLock $lock
     */
    public $lock;

    /**
     * @var libCache $cache
     */
    public $cache;

    /**
     * @var \Ext\libQueue $queue
     */
    public $queue;

    /**
     * @var \Ext\libCrypt $crypt
     */
    public $crypt;

    /** @var string $env */
    public $env = 'prod';

    /**
     * @var \Core\Factory|\Ext\libConfGet $conf
     */
    public $conf;

    /**
     * base constructor.
     */
    public function __construct()
    {
        //判断环境设置
        if (is_file($env_file = realpath(App::new()->root_path . '/conf/.env'))) {
            $env = trim(file_get_contents($env_file));

            if (is_file($conf_file = realpath(App::new()->root_path . '/conf/' . $env . '.conf'))) {
                $this->env = &$env;
            }
        }

        //加载配置
        $this->conf = libConfGet::new('conf')->load($this->env);

        //初始化配置
        self::init();

    }

    /**
     * 初始化配置
     */
    public function init(): void
    {
        $this->mysql = libMySQL::new()->bindPdo(libPDO::new($this->conf->use('mysql'))->connect());
        $this->redis = libRedis::new($this->conf->use('redis'))->connect();
        $this->lock  = libLock::new()->bindRedis($this->redis);
        $this->cache = libCache::new()->bindRedis($this->redis);
        $this->queue = libQueue::new()->bindRedis($this->redis);
        $this->crypt = libCrypt::new();
    }

    /**
     * 生成随意
     *
     * @param int $length
     *
     * @return string
     */
    public function rand_str(int $length)
    {
        $key     = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ'; //字符池
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern[mt_rand(0, 62)]; //生成php随机数
        }
        return $key;
    }

}