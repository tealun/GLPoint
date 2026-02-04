<?php
declare (strict_types=1);

namespace woo\common\abstracts;

abstract class CallbackAbstract
{
    protected $controller;
    protected $c;
    protected $a;
    protected $ca;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->c          = $this->controller->params['controller'] ?? '';
        $this->a          = $this->controller->params['action'] ?? '';
        $this->ca         = $this->controller->request->controller() .  '::' . $this->a;
    }

    abstract public function before();

    abstract public function after();
}