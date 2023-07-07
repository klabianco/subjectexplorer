<?php

$userAuthToken = $_POST['userAuthToken'];

$U = new User();
$U->setDb($Db);
$U->setAuthToken($userAuthToken);
$U->loadByAuthToken();

if($U->getId() != ''){
    $activities = $Db->prepExecFetchAll("SELECT id, activity, subject, added_date FROM `activities` WHERE `user_id` = :user_id ORDER BY `added_date` DESC", [
        'user_id' => $U->getId()
    ]);
    echo json_encode($activities);
} else {
    echo json_encode(['error' => 'There was an error getting the activities.']);
}