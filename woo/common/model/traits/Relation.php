<?php
declare (strict_types=1);

namespace woo\common\model\traits;

use think\Exception;
use woo\common\helper\Arr;
use woo\common\helper\Str;

trait Relation
{

    /**
     * 关联预载入 In方式
     * @param array|string $with 关联方法名称
     * @return $this
     */
    public function with($with)
    {
        return parent::with($this->parseWith($with));
    }

    /**
     * 关联预载入 JOIN方式
     * @param array|string $with     关联方法名
     * @param string       $joinType JOIN方式
     * @return $this
     */
    public function withJoin($with, string $joinType = '')
    {
        return parent::withJoin($this->parseWith($with), $joinType);
    }

    /**
     * with解析
     * @param $with
     * @return array
     */
    public function parseWith($with)
    {
        //$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
        if (empty($with)) {
            return [];
        }
        if (is_string($with)) {
            $with = explode(',', $with);
        }

        $with = Arr::normalize($with);
        foreach ($with as $key => &$relation) {
            if (is_callable($relation)) {
                continue;
            }
            if (!is_array($relation)) {
                if (empty($relation)) {
                    $relation = [];
                } else {
                    continue;
                }
            }

            $assocMsg = $this->relationLink[$key];
            $relation = function($query) use($relation, $assocMsg) {
                foreach ($relation as $key1 => $value1) {
                    if (is_numeric($key1)) {
                        $relation['with'][] = $value1;
                    }
                }
                // 关联条件
                if (isset($assocMsg['where'])) {
                    $query->where($assocMsg['where']);
                }
                if (isset($relation['where'])) {
                    $query->where($relation['where']);
                }
                // 关联字段
                $field = [];
                if (isset($assocMsg['field'])) {
                    $field = $assocMsg['field'];
                }
                if (isset($relation['field'])) {
                    $field = $relation['field'];
                }
                if (!empty($field)) {
                    // hasOne 、hasMany 类型中字段列表必须有外键字段 否则报错
                    if (in_array($assocMsg['type'], ['hasOne', 'hasMany'])) {
                        $foreignKey = $assocMsg['foreignKey'] ?? Str::snake($assocMsg['localName']) . '_id';
                        if (!in_array($foreignKey, $field)) {
                            $field[] = $foreignKey;
                        }
                    }
                    if (in_array($assocMsg['type'], ['belongsTo'])) {
                        $localKey = $assocMsg['localKey'] ?? model($assocMsg['foreign'])->getPk();
                        if (!in_array($localKey, $field)) {
                            $field[] = $localKey;
                        }
                    }
                    $query->withField($field);
                    if (in_array($assocMsg['type'], ['hasMany', 'belongsToMany'])) {
                        $query->field($field);
                    }
                }

                //排序
                $order = [];
                if (isset($assocMsg['order'])) {
                    $order = $assocMsg['order'];
                }
                if (isset($relation['order'])) {
                    $order = $relation['order'];
                }
                if (!empty($order)) {
                    $query->order($order);
                }

                if (isset($relation['limit'])) {
                    $query->limit($relation['limit']);
                } else {
                    if (isset($assocMsg['limit'])) {
                        $query->limit($assocMsg['limit']);
                    }
                }
                // 嵌套关联不支持更多属性， 比如where、order等 ， 但支持按tp原本的所有语法 比如闭包
                if (isset($relation['with'])) {
                    $query->with($relation['with']);
                }
            };
        }
        return $with;
    }


    /**
     * belongsTo关联自动调用
     */
    protected function belongsToCall($relationModel, $relationMsg = [])
    {
        if (!isset($relationMsg['foreignKey'])) {
            $foreignKey = Str::snake($relationModel) . '_id';
        } else {
            $foreignKey = $relationMsg['foreignKey'];
        }
        $localKey = $relationMsg['localKey'] ?? '';
        $joinType = $relationMsg['joinType'] ?? 'inner';

        return $this->belongsTo(get_model_name($relationMsg['foreign']), $foreignKey, $localKey)->joinType($joinType);
    }

    /**
     * hasOne关联自动调用
     */
    protected function hasOneCall($relationModel, $relationMsg = [])
    {
        $foreignKey = $relationMsg['foreignKey'] ?? '';
        $localKey = $relationMsg['localKey'] ?? '';
        $joinType = $relationMsg['joinType'] ?? 'inner';
        return $this->hasOne(get_model_name($relationMsg['foreign']), $foreignKey, $localKey)->joinType($joinType);
    }

    /**
     * hasMany关联自动调用
     */
    protected function hasManyCall($relationModel, $relationMsg = [])
    {
        $foreignKey = $relationMsg['foreignKey'] ?? '';
        $localKey = $relationMsg['localKey'] ?? '';
        return $this->hasMany(get_model_name($relationMsg['foreign']), $foreignKey, $localKey);
    }

    /**
     * belongsToMany关联自动调用
     */
    protected function belongsToManyCall($relationModel, $relationMsg = [])
    {
        $middle = $relationMsg['middle'] ?? Str::studly($this->name) . Str::studly($relationModel);
        if (get_model_name($middle)) {
            $middle = get_model_name($middle);
        }
        $foreignKey = $relationMsg['foreignKey'] ?? Str::snake($relationModel) . '_id';
        $localKey = $relationMsg['localKey'] ?? Str::snake($this->name) . '_id';
        return $this->belongsToMany(get_model_name($relationMsg['foreign']), $middle, $foreignKey, $localKey);
    }

    /**
     * hasManyThrough关联自动调用
     */
    protected function hasManyThroughCall($relationModel, $relationMsg = [])
    {
        if (!isset($relationMsg['through'])) {
            throw new \Exception('未通过through属性设置远程关联模型属性');
        }
        $foreignKey = $relationMsg['foreignKey'] ?? Str::snake($this->name) . '_id'; // 中间表中 关联 当前表 的关联字段
        $throughKey = $relationMsg['throughKey'] ?? Str::snake($relationMsg['through']) . '_id';// 关联表中 关联 中间表 的关联字段 如果二级目录 一定直接自定义 否则获取错误
        return $this->hasManyThrough(get_model_name($relationMsg['foreign']), get_model_name($relationMsg['through']), $foreignKey, $throughKey);
    }

    /**
     * hasOneThrough关联自动调用
     */
    protected function hasOneThroughCall($relationModel, $relationMsg = [])
    {
        if (!isset($relationMsg['through'])) {
            throw new \Exception('未通过through属性设置远程关联模型属性');
        }
        $foreignKey = $relationMsg['foreignKey'] ?? Str::snake($this->name) . '_id'; // 中间表中 关联 当前表 的关联字段
        $throughKey = $relationMsg['throughKey'] ?? Str::snake($relationMsg['through']) . '_id';// 关联表中 关联 中间表 的关联字段 如果二级目录 一定直接自定义 否则获取错误
        return $this->hasOneThrough(get_model_name($relationMsg['foreign']), get_model_name($relationMsg['through']), $foreignKey, $throughKey);
    }

    /**
     * belongsToThrough关联自动调用
     */
    protected function belongsToThroughCall($relationModel, $relationMsg = [])
    {
        if (!isset($relationMsg['through'])) {
            throw new \Exception('未通过through属性设置远程关联模型属性');
        }
        $foreignKey = $relationMsg['foreignKey'] ?? Str::snake($relationModel) . '_id';// 中间表中 关联 关联表 关联字段
        $throughKey = $relationMsg['throughKey'] ?? Str::snake($relationMsg['through']) . '_id';// 当前表中 关联 中间表 关联字段  如果二级目录 一定直接自定义 否则获取错误
        $throughPk = model($relationMsg['through'])->getPk();// 中间表 主键
        $relationPk = model($relationMsg['foreign'])->getPk();// 远程关联表 主键
        return $this->hasOneThrough(get_model_name($relationMsg['foreign']), get_model_name($relationMsg['through']), $throughPk, $relationPk, $throughKey, $foreignKey);
    }
}