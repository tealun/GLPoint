<?php
declare (strict_types=1);

namespace woo\common\service;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use woo\common\CachedReader;
use think\App;
use think\Cache;
use think\Config;

class WooService extends \think\Service
{
    protected $reader;


    public function register()
    {
        AnnotationReader::addGlobalIgnoredName('mixin');

        // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
        AnnotationRegistry::registerLoader('class_exists');

        $this->app->bind(Reader::class, function (App $app, Config $config, Cache $cache) {

            $store = $config->get('annotation.store');

            return new CachedReader(new AnnotationReader(), $cache->store($store), $app->isDebug());
        });
    }
}