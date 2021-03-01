<?php
/**
 * Created by PhpStorm.
 * User: Reck
 * Date: 2021/2/26
 * Time: 10:49
 * Note: api.php
 */

require __DIR__ . '/../../NervSys/NS.php';

//可选，如果需要的话，请参阅"Ext/libCoreApi.php"
\Ext\libCoreApi::new()
    //打开核心调试模式 (错误信息会随着结果显示出来)
    ->setCoreDebug(true)
    //打开全局跨域许可 (默认请求头)
    ->addCorsRecord('*')
    //设置文件项目录
    ->setApiPath('admin')
    //设置输出格式为"application/json; charset=utf-8"
    ->setContentType('application/json');

NS::new();