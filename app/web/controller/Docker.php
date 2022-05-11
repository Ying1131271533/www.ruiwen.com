<?php

namespace app\web\controller;


class Docker
{
    public function index()
    {
        return success('docker:6001-'.md5(time()));
    }
}
