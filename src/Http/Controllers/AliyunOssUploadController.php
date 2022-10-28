<?php

namespace VRobin\AliyunOssUpload\Http\Controllers;

use Encore\Admin\Facades\Admin;
use Iidestiny\Flysystem\Oss\OssAdapter;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class AliyunOssUploadController extends Controller
{
    public function index()
    {
        if (!Admin::user()) {
            return ['error' => '无权访问'];
        }
        $path = request()->input('path', '/');
        $file = request()->input('name');
        /**
         * @var $storage OssAdapter
         */
        $storage = Storage::disk('oss');
        $cfg = $storage->signatureConfig($path, null, [], 360);
        $cfg = json_decode($cfg, true);

        $fileInfo = pathinfo($file);
        $md5 = md5($file . rand(10000, 99999) . time());
        $ext = $fileInfo['extension'] ?? 'file';
        $cfg['filename'] = "{$cfg['dir']}/{$md5}.{$ext}";
        return $cfg;
    }
}