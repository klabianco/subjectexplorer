<?php

trait IdAddedDate{
    use Id;
    private $_addedDate;

    public function hasId(){
        if($this->getId() == '') return false;
        return true;
    }

    public function setAddedDate($s){
        $this->_addedDate = $s;
    }

    public function getAddedDate(){
        return $this->_addedDate;
    }
}