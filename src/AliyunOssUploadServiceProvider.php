<?php

namespace VRobin\AliyunOssUpload;

use Encore\Admin\Facades\Admin;
use Illuminate\Support\ServiceProvider;

class AliyunOssUploadServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(AliyunOssUpload $extension)
    {
        if (!AliyunOssUpload::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-aliyun-oss');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/aliyun-oss-upload')],
                'laravel-admin-aliyun-oss'
            );
        }

        $this->app->booted(function () {
            AliyunOssUpload::routes(__DIR__ . '/../routes/web.php');
        });
        $r = url(config('admin.route.prefix').'/aliyun-oss-upload');
        Admin::script(
            <<<EOT
        window.ALI_UPLOADER_URL = "{$r}";
EOT
        );
    }

}