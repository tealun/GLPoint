<?php
namespace woo\common\model;


class Forge
{

    public $form = [];
    public $pk = '';
    public $display = '';


    public function setTableFields(array $table)
    {
        $this->form = $table;
        return $this;
    }

    public function getTableFields()
    {
        return array_keys($this->form);
    }

    public function setPk(string $pk)
    {
        $this->pk = $pk;
        return $this;
    }
    public function setDisplay(string $display)
    {
        $this->display = $display;
        return $this;
    }

    public function getPk()
    {
        return $this->pk ;
    }


}