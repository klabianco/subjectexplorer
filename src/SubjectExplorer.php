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

            $prompt = 'Activity: "' . $this->activity . '”
        
Only write bullet points of how the '.$grade.' child has learned specific concepts from the activity in the subject of "' . $this->subject . '". Do not assume the child used any materials beyond those mentioned in the description. Output in html starting with <ul>. Include a <p> short paragraph for tips on creative ways for continued development related to the activity.';

            $AI = new AI();
            $AI->setPrompt($prompt);

            $response = $AI->getResponseFromOpenAi();

            return $response;
        }
    }
}
