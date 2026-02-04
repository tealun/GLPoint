<?php
declare (strict_types=1);

namespace woo\common\model\db;

use think\Collection;
use woo\common\helper\Str;

class Query extends \think\db\Query
{
    public function find($data = null)
    {
        $this->checkOptionsForWooType();
        return parent::{__FUNCTION__}($data);
    }
    public function select($data = null): Collection
    {
        $this->checkOptionsForWooType();
        return parent::{__FUNCTION__}($data);
    }
    protected function checkOptionsForWooType()
    {
        $options = $this->getOptions();
        if (empty($options['field'])) {
            $this->field(true);
        }
        if (!isset($options['alias'])) {
            if (empty($options['table'])) {
                $this->alias(Str::snake($this->getName()));
            } else {
                $this->alias(Str::snake(substr($options['table'], strlen($this->connection->getConfig('prefix')))));
            }
        }
    }

}