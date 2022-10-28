<?php

use Illuminate\Support\Facades\Route;
use VRobin\AliyunOssUpload\Http\Controllers\AliyunOssUploadController;

Route::any('aliyun-oss-upload', AliyunOssUploadController::class.'@index')->name('laravel-admin-aliyun-oss');