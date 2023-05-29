<?php

namespace VRobin\AliyunOssUpload;

use Encore\Admin\Admin;
use Encore\Admin\Form\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Created by PhpStorm.
 * User: Jiankang Wang
 * Date: 2022/10/2022/10/27
 * Time: 14:16
 */
class OssUploader extends Field
{
    public static $headerJs = [
    ];
    protected static $css = [
    ];
    protected $view = 'laravel-admin-aliyun-oss::file';
    protected static $sessionKey;

    protected $variables = [
        'path' => '',
        'maxCount' => 1,
        'accept' => '*',
        'files' => []
    ];

    protected $storage;
    protected $isMulti = false;

    protected $path;
    /**
     * @var string
     */
    protected $pathColumn;

    /**
     * Initialize the storage instance.
     *
     * @return void.
     * @throws \Exception
     */
    protected function initStorage()
    {
        $this->disk(config('admin.upload.disk'));
    }

    /**
     * Set disk for storage.
     *
     * @param string $disk Disks defined in `config/filesystems.php`.
     *
     * @return $this
     * @throws \Exception
     *
     */
    public function disk($disk)
    {
        try {
            $this->storage = Storage::disk($disk);
        } catch (\Exception $exception) {
            if (!array_key_exists($disk, config('filesystems.disks'))) {
                admin_error(
                    'Config error.',
                    "Disk [$disk] not configured, please add a disk config in `config/filesystems.php`."
                );

                return $this;
            }

            throw $exception;
        }

        return $this;
    }

    public function path($path)
    {
        $this->path = $path;
        $this->variables['path'] = $path;
        return $this;
    }

    public function move($path)
    {
        return $this->path($path);
    }

    /**
     * 最大上传数量
     * @param int $maxCount
     * @return OssUploader
     */
    public function multi(int $maxCount = 99): OssUploader
    {
        $this->variables['maxCount'] = $maxCount;
        return $this;
    }

    public function video(): OssUploader
    {
        $this->variables['accept'] = 'video/*';
        return $this;
    }

    public function image(): OssUploader
    {
        $this->variables['accept'] = 'image/*';
        return $this;
    }


    public function variables(): array
    {
        $variables = parent::variables();
        $variables['files'] = $this->files($variables['value']);
        $variables['value'] = json_encode($variables['value']);

        return $variables;
    }

    public function render()
    {

        Admin::headerJs(self::$headerJs);
        $this->setSessionKey();

        $el = $this->getElementClassSelector();
        $name = $this->elementName ?: $this->formatName($this->column);


        Admin::script(<<<EOT
bootVue('{$el}','{$name}');

EOT
        );

        return parent::render();
    }


    protected function files($files)
    {
        return collect($files)->map(function ($file) {
            return ['path' => $file, 'url' => $this->objectUrl($file)];
        })->toJson();
    }

    protected function isMulti(): bool
    {
        return $this->variables['maxCount'] > 1;
    }

    protected function setSessionKey()
    {
        if (self::$sessionKey) {
            return;
        }
        self::$sessionKey = uniqid();
        Admin::script(<<<EOT
        vueContainer=[];
        \$form=null;
EOT
        );
    }

    public function prepare($value)
    {
        $files = $value ? json_decode($value, true) : [];
        if ($this->isMulti()) {
            return $files;
        }
        return $files[0] ?: '';
    }

    public function objectUrl($path)
    {
        if ($this->pathColumn && is_array($path)) {
            $path = Arr::get($path, $this->pathColumn);
        }

        if (URL::isValidUrl($path)) {
            return $path;
        }

        if ($this->storage) {
            return $this->storage->url($path);
        }

        return Storage::disk(config('admin.upload.disk'))->url($path);
    }

    /**
     * Set path column in has-many related model.
     *
     * @param string $column
     *
     * @return $this
     */
    public function pathColumn($column = 'path')
    {
        $this->pathColumn = $column;

        return $this;
    }

}
