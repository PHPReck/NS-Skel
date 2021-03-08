<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 3/17/2020
 * Time: 8:18 PM
 * Note: verify.php
 */

namespace app\lib;


use Core\Lib\IOUnit;
use Ext\libLog;

class verify extends errno
{
    const ADMIN_KEY = '';
    const WEB_KEY   = '';

    //过滤保留字段（不过滤）
    const ESCAPE_CHECK = [];

    const ESCAPE_CMD = [];

    public function check(int $check_type): void
    {
        $ioUnit = IOUnit::new();
        $data_pack = $ioUnit->src_input;

        if (in_array($ioUnit->src_cmd, self::ESCAPE_CMD)) return;

        //检查签名参数
        if (!isset($data_pack['u'])
            || !isset($data_pack['t'])
            || !isset($data_pack['s'])) {
            $this->fail(10011);
        }

        //检查时间戳合法性
        $data_pack['t'] = (string)((int)$data_pack['t']);

        if (strlen($data_pack['t']) < 10) {
            $this->fail(10011);
        }

        //提取签名
        $data_sign = &$data_pack['s'];
        unset($data_pack['s']);

        //获取时间戳末位整形
        $skip_id = (int)substr($data_pack['t'], -1, 1);

        //重组key
        $key_const = $check_type ? self::WEB_KEY : self::ADMIN_KEY;
        $key_arr   = str_split($key_const);
        unset($key_arr[$skip_id]);
        $app_key = implode($key_arr);

        //并入数据包
        $data_pack['k'] = &$app_key;

        //按key升序排列
        ksort($data_pack);

        //开始合并非数组类参数
        $data_string = '';

        foreach ($data_pack as $k => $value) {
            if (in_array($k, self::ESCAPE_CHECK) || is_array($value) || is_object($value)) {
                continue;
            }
            $data_string .= (string)$value;
        }

        //计算md5签名
        $data_hash = hash('md5', $data_string);

        //对比签名
        if ($data_sign !== $data_hash) {
            //记录日志
            libLog::new('data_verify')
                ->add([
                    'ip'          => core::get_ip(),
                    'cmd'         => core::get_cmd_val(),
                    'client_sign' => &$data_sign,
                    'server_sign' => &$data_hash,
                    'sign_data'   => &$data_pack,
                    'input_data'  => core::get_data()
                ])
                ->save();

            //退出
            $this->fail(10012);
        }
    }
}