<?php


namespace app\cli;


use app\lib\base;

class crond extends base
{
    public function test()
    {
        var_dump('111');
        return ['test' => '11'];
    }
}