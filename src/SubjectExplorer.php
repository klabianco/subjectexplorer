<?php

class SubjectExplorer
{
    public $activity = '', $subject = '', $grade = '';

    public function getResponseFromOpenAi()
    {
        if ($this->activity != '' && $this->subject != '') {
            require_once 'AI.php';

            $grade = '';
            if($this->grade != '') $grade = $this->grade.'-grade';

            $prompt = 'Description: "' . $this->activity . '”
        
Only write individual bullet points of how the '.$grade.' child has already learned specific concepts in the subject of "' . $this->subject . '". Do not assume the child used any materials beyond those mentioned in the description. Output in html starting with <ul>.  After the list, output a <p> student "I can" statement.';

            $AI = new AI();
            $AI->setPrompt($prompt);

            $response = $AI->getResponseFromOpenAi();

            return $response;
        }
    }
}
