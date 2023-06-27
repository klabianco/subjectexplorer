<?php

trait UserId{
    private $_userId;

    public function getUserId()
    {
        return $this->_userId;
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function dbReplaceUserIdForTable($search,$replace,$table){
        global $db;

        $q = "select count(*) c from `".$table."` where user_id = :search";
        $d = [':search' => $search];
        $count = $db->prepExecFetchColumn($q, $d);

        if ($count > 0) {
            $q = "UPDATE `".$table."` SET `user_id` = :replace WHERE user_id = :search";
            $d[':replace'] = $replace;
            $db->prepExec($q, $d);
        }

        if ($count == 0) return false;
        return true;
    }
}