<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 3/17/2020
 * Time: 8:15 PM
 * Note: api.php
 */

namespace app\lib;

use app\lib\enum\enum_lock_cmd;
use app\lib\model\sys_config;
use Core\Lib\IOUnit;

/**
 * Class api
 *
 * 所有对外 API 暴露类请继承这个，省的写 $tz 了
 *
 * @package app\lib
 */
class api extends errno
{
    public $tz = '*';

    public $user_id;

    public $check_token = true;

    public $check_type = 0;

    public $check_sign = true;

    public $head_match = '/(http:\/\/)|(https:\/\/)/i';

    //过滤保留字段（不过滤）
    const ESCAPE_EXCLUDE = ['token_user', 'token'];

    CONST SCRIPT_PARAM = ['text_content'];

    CONST INJECT_WORDS = ['script', 'js'];

    /**
     * @var \Core\Factory|\Core\Lib\IOUnit $ioUnit
     */
    public $ioUnit;

    public $img_host;


    /**
     * api constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->ioUnit = IOUnit::new();

        //入参过滤
        $this->escape($this->ioUnit->src_input);

        //数据签名
        if ($this->check_sign) verify::new()->check($this->check_type);

        $this->get_img_host();

    }

    /**
     * 过滤输入
     *
     * @param array $input_data
     */
    private function escape(array &$input_data): void
    {
        foreach ($input_data as $key => &$value) {
            if (is_array($value)) {
                $this->escape($value);
                continue;
            }

            if (in_array($key, self::ESCAPE_EXCLUDE, true)) {
                continue;
            }

            if (is_string($value)) {
                if (in_array($key, self::SCRIPT_PARAM, true)) {
                    $value = str_ireplace(self::INJECT_WORDS, [''], $value);
                }
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
    }

    public function input_lock()
    {
        $token_app = $this->ioUnit->src_input['token'];
        if (empty($token_app)) {
            return true;
        }
        $cmd = $this->ioUnit->src_cmd;
        if (!in_array($cmd, enum_lock_cmd::$lock_cmd, true)) {
            return true;
        }
        $lock_res = $this->lock->on($cmd . ":" . $token_app);
        if (!$lock_res) {
            $this->fail(10014);
        }
        return true;
    }

    public function get_img_host()
    {
        $img_setting = $this->img_setting();
        if (empty($img_setting)) $this->img_host = $this->conf->use('img')['host'];
        else {
            if ($img_setting['default'] == 'local') $this->img_host = $this->conf->use('img')['host'];
            else $this->img_host = $img_setting[$img_setting['default']]['domain'] ?? "";
        }
    }

    public function img_setting()
    {
        $storage = $this->redis->get('setting:storage');
        if (empty($storage)) {
            //$storage = sys_config::new()->config_info('storage');
            if (empty($storage)) $storage = '{}';
        }
        return json_decode($storage, true);
    }

    public function get_user(string $token)
    {
        $res = token::new()->parse($token, '_user_id');
        if ($res['status'] !== 0) return 0;
        return $res['data']['_user_id'];
    }

    public function deal_head($head_img)
    {
        return preg_match($this->head_match, $head_img) == 0 ? $this->img_host . $head_img : $head_img;
    }
}