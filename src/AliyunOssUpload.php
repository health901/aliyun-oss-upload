<?php

namespace VRobin\AliyunOssUpload;

use Encore\Admin\Extension;

class AliyunOssUpload extends Extension
{
    public $name = 'aliyun-oss-upload';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Aliyunossupload',
        'path'  => 'aliyun-oss-upload',
        'icon'  => 'fa-gears',
    ];


}