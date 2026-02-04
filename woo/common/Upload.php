<?php
declare (strict_types=1);

namespace woo\common;


use think\facade\Config;
use woo\common\helper\Str;

class Upload
{
    /**
     * 上传实例
     * @var array
     */
    protected $instance = [];

    /**
     * 上传配置
     * @var array
     */
    protected $config = [];

    /**
     * 操作句柄
     * @var object
     */
    protected $handler;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->init($config);
    }

    protected function init(array $options = [], $force = false)
    {
        if (!isset($this->handler) || $force) {
            if (!empty($options['model']) && get_model_name($options['model']) && !empty($options['field'])) {
                try {
                    $model = model($options['model']);
                    if (isset($model->form[$options['field']]['upload'])) {
                        $this->setConfig($model->form[$options['field']]['upload']);
                    }
                } catch (\Exception $e) {
                    throw  new \Exception($e->getMessage());
                }
            }
            $this->handler = $this->connect();
        }
        return $this->handler;
    }

    protected function connect($name = false)
    {
        if (empty($this->config['type'])) {
            $this->config['type'] = Config::get('wooupload.default') ?: 'local';
        }
        $this->config = array_merge(Config::get('wooupload.drivers')[$this->config['type']] ?? [], $this->config);
        if (false === $name) {
            $name = md5(serialize($this->config));
        }
        if (false === $name || !isset($this->instance[$name])) {
            if (true === $name) {
                $name = md5(serialize($this->config));
            }
            if (isset($this->config["driver"])) {
                if (strpos($this->config["driver"], "\\") === false) {
                    $driver = "\\woo\\common\\upload\\driver\\" . Str::studly($this->config["driver"]);
                } else {
                    $driver = $this->config["driver"];
                }
            } else {
                $driver = "\\woo\\common\\upload\\driver\\" . Str::studly($this->config["type"]);
            }
            $this->instance[$name] = app($driver, [$this->config]);
        }
        return $this->instance[$name];
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->init(), $method], $args);
    }
}