<?php
namespace woo\common\builder\table\traits;

trait ColParse
{
    protected function checkerBeforeParse($info, $model)
    {
        if (isset($model->form[$info['field']]['options'])) {
            $info['options'] = $model->form[$info['field']]['options'];
        }
        if (!isset($info['options']['yes'])) {
            $info['options']['yes'] = 1;
        }
        if (!isset($info['options']['no'])) {
            $info['options']['no'] = 0;
        }
        if (isset($model->form[$info['field']]['attrs']['title'])) {
            $model->form[$info['field']]['attrs']['lay-text'] = $model->form[$info['field']]['attrs']['title'];
        }
        if (isset($model->form[$info['field']]['attrs']['lay-text'])) {
            $info['lay-text'] = $model->form[$info['field']]['attrs']['lay-text'];
        } else {
            $info['lay-text'] = 'Y|N';
        }
        return $info;
    }
}