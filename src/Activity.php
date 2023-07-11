<?php

class Activity{
    use IdAddedDate, UserId;

    private $_activity, $_subject, $_grade, $_response;

    public function __construct($id = null)
    {
        if($id != null){
            $this->setId($id);
            $this->dbLoadById();
        }
    }

    public function getActivity()
    {
        return $this->_activity;
    }

    public function setActivity($activity)
    {
        $this->_activity = $activity;
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function getSubjectString(){
        $subjectString = '';
        foreach($this->getSubject() as $subject){
            $subjectString .= $subject.', ';
        }
        return rtrim($subjectString, ', ');
    }

    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    public function getGrade()
    {
        return $this->_grade;
    }

    public function setGrade($grade)
    {
        $this->_grade = $grade;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setResponse($response)
    {
        $this->_response = $response;
    }

    // has grade
    public function hasGrade(){
        if($this->getGrade() == '') return false;
        return true;
    }

    // has response

    public function hasResponse(){
        if($this->getResponse() == '') return false;
        return true;
    }

    // has subject

    public function hasSubject(){
        if($this->getSubject() == '') return false;
        return true;
    }

    public function dbInsert(){
        global $Db;

        $q = "INSERT INTO `activities` (`activity`, `subject`, `grade`, `response`,`added_date`, `user_id`) VALUES (:activity, :subject, :grade, :response,NOW(), :user_id)";
        $d = [
            ':activity' => $this->getActivity(),
            ':subject' => $this->getSubjectString(),
            ':grade' => $this->getGrade(),
            ':response' => $this->getResponse(),
            ':user_id' => $this->getUserId()
        ];

        $Db->prepExec($q, $d);

        $this->setId($Db->getLastInsertId());
    }

    public function dbLoadById(){
        global $Db;

        $q = "SELECT * FROM `activities` WHERE `id` = :id";
        $d = [
            ':id' => $this->getId()
        ];

        $activity = $Db->prepExecFetchAll($q, $d);

        if(count($activity) > 0){
            $this->setActivity($activity[0]['activity']);
            $this->setSubject($activity[0]['subject']);
            $this->setGrade($activity[0]['grade']);
            $this->setResponse($activity[0]['response']);
            $this->setAddedDate($activity[0]['added_date']);
            $this->setUserId($activity[0]['user_id']);
        } else {
            $this->setId('');
        }
    }

    public function dbUpdateUserId(){
        global $Db;

        $q = "UPDATE `activities` SET `user_id` = :user_id WHERE `id` = :id";
        $d = [
            ':user_id' => $this->getUserId(),
            ':id' => $this->getId()
        ];

        $Db->prepExec($q, $d);
    }

    public function dbRemoveUserId(){
        global $Db;

        $q = "UPDATE `activities` SET `user_id` = NULL WHERE `id` = :id";
        $d = [
            ':id' => $this->getId()
        ];

        $Db->prepExec($q, $d);
    }

    public function getAndSetResponseFromOpenAi()
    {
        if ($this->getActivity() != '' && $this->getSubjectString() != '') {
            $grade = '';
            if($this->getGrade() != '') $grade = $this->getGrade().'-grade';

            $prompt = 'Activity: "' . $this->getActivity() . '”
        
Only write bullet points of how the '.$grade.' child has learned specific concepts from the activity for the subject of ' . $this->getSubjectString() . '. Do not assume the child used any materials beyond those mentioned in the description. Output in html starting with <ul>. Include a <p> short paragraph for tips on creative ways for continued development related to the activity.';

            $AI = new AI();
            $AI->setPrompt($prompt);

            $response = $AI->getResponseFromOpenAi();

            $this->setResponse($response);
        }
    }
}