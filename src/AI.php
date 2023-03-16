<?php

use \Curl\Curl;

class AI
{
    private $_prompt;

    public function setPrompt($prompt)
    {
        $this->_prompt = $prompt;
    }

    public function getPrompt()
    {
        return $this->_prompt;
    }

    public function hasPrompt()
    {
        if ($this->getPrompt() == '') return false;
        return true;
    }

    public function getResponseFromOpenAi()
    {
        if ($this->hasPrompt()) {
            $openAIKey = $_SERVER['OPENAI_API_KEY'];

            $url = "https://api.openai.com/v1/chat/completions";
            $maxTokens = 1500;

            $curl = new Curl();
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setopt(CURLOPT_TIMEOUT, 120);
            
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', 'Bearer ' . $openAIKey);

            $msgs = [['role' => 'user', 'content' => $this->getPrompt()]];

            $curl->post($url, [
                'model' => 'gpt-3.5-turbo',
                'messages' => $msgs,
                'temperature' =>  0,
                'max_tokens' => $maxTokens,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0
            ]);

            if ($curl->error) {
                echo 'Error: ' . $curl->errorMessage . "\n";
                $curl->diagnose();
            } else { // returns only the text from the first choice - there may be many choices...
                return trim($curl->response->choices[0]->message->content);
            }
        } else return false;
    }
}
