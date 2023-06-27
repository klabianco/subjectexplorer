<?php

trait Id{
    private $_id;

    public function setId($i){
        $this->_id = $i;
    }

    public function getId(){
        return $this->_id;
    }
}