<?php

// accepts the activity id and the user auth token
// removes the user id from the activity

$id = $_POST['id'];
$userAuthToken = $_POST['userAuthToken'];

$A = new Activity($id);
$U = new User();
$U->setDb($Db);
$U->setAuthToken($userAuthToken);
$U->loadByAuthToken();

if($A->getUserId() != $U->getId()){
    echo "Error: You do not have permission to delete this activity.";
    exit;
} else {
    $A->dbRemoveUserId();
}
