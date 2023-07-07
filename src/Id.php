<?php

trait Id{
    private $_id;

    public function setId($i){
        $this->_id = $i;
    }

    public function getId(){
        return $this->_id;
    }

    public function hasId(){
        if($this->getId() == '') return false;
        return true;
    }
}