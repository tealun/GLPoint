<?php
use think\facade\Route;
// 自行use think\facade\Route;
Route::get('/captcha/[:config]', "\\think\\captcha\\CaptchaController@index");
// 防止宝塔伪静态禁用
Route::get('/app_center/:action', "Application/:action");