<?php

class SubjectExplorer
{
    public $activity = '', $subject = '';

    public function getResponseFromOpenAi()
    {
        if ($this->activity != '' && $this->subject != '') {
            require_once 'AI.php';

            $prompt = 'Description: "' . $this->activity . '”
        
        Only write individual bullet points of how the child in the description has already learned specific concepts in the subject of "' . $this->subject . '". Do not assume the child used any materials beyond those mentioned in the description. Output in html starting with <ul>';

            $AI = new AI();
            $AI->setPrompt($prompt);
            $response = $AI->getResponseFromOpenAi();

            return $response;
        }
    }
}
