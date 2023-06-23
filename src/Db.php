<?php

class Db
{
    public $con;

    public function __construct()
    {
        try {
            $this->con = new PDO('mysql:host=' . $_SERVER["DB_HOST"] . ';dbname=' . $_SERVER["DB_NAME"], $_SERVER["DB_USER"], $_SERVER["DB_PW"], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            print "Sorry, there was a connection error.  Please try again later.";
            print "Error: " . $e->getMessage();
        }
    }

    public function getLastInsertId()
    {
        return $this->con->lastInsertId();
    }

    public function prepExecFetchColumn($s, $q = array())
    {
        $q = $this->prepExec($s, $q);
        return $q->fetchColumn();
    }

    public function fetch($q)
    {
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($q)
    {
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prepExec($s, $d = [])
    {
        $q = $this->con->prepare($s);
        $q->execute($d);
        return $q;
    }

    public function prepExecFetch($s, $d)
    {
        $q = $this->prepExec($s, $d);
        return $this->fetch($q);
    }

    public function prepExecFetchAll($s, $d = array())
    {
        $q = $this->prepExec($s, $d);
        return $this->fetchAll($q);
    }

    public function updateThis($table, $set, $data)
    {
        $q = 'UPDATE `' . $table . '` SET ' . $set . ' WHERE `id` = :id';
        $this->prepExec($q, $data);
    }

    public function dbGetFieldsForId($table, $fields, $id)
    {
        $q = "SELECT " . $fields . " FROM `" . $table . "` where id = :id";
        $d = [':id' => $id];

        return $this->prepExecFetch($q, $d);
    }

    public function deleteThis($table, $field = "id", $id)
    {
        $q = "DELETE FROM " . $table . " WHERE `" . $field . "` = :id";
        $d = [':id' => $id];
        $this->prepExec($q, $d);
    }
}