<?php

class AI
{
    private string $input;
    private string $prompt;
    private string $taskString;
    private array $uimIds;
    private string $user;

    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUIMIds(array $ids): void
    {
        $this->uimIds = $ids;
    }

    public function getUIMIds(): array
    {
        return $this->uimIds;
    }

    public function setTaskString(string $string): void
    {
        $this->taskString = $string;
    }

    public function getTaskString(): string
    {
        return $this->taskString;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function hasPrompt(): bool
    {
        return !empty($this->getPrompt());
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function setInput(string $input): void
    {
        $this->input = $input;
    }

    public function getResponseFromOpenAi($systemRole = "You are a helpful teacher.", $temperature = 1.0, $frequencyPenalty = 0, $model = "gpt-3.5-turbo",$maxTokens = 2000): mixed
    {
        if (!$this->hasPrompt()) {
            return false;
        }

        $yourApiKey = $_SERVER['OPENAI_API_KEY'];
        $client = OpenAI::client($yourApiKey);

        $response = $client->chat()->create([
            'model' => $model,
            'messages' => [
                [
                    "role" => "system",
                    "content" => $systemRole
                ],
                [
                    "role" => "user",
                    "content" => $this->getPrompt()
                ]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'frequency_penalty' => $frequencyPenalty,
            'presence_penalty' => 0,
        ]);

        $content = $response->choices[0]->message->content;

        return $content;
    }

    public function streamTest()
    {

        $openAIKey = $_SERVER['OPENAI_API_KEY'];

        $openAi = new OpenAi($openAIKey);

        $opts = [
            'prompt' => "Hello",
            'temperature' => 0.9,
            "max_tokens" => 3000,
            "frequency_penalty" => 0,
            "presence_penalty" => 0.6,
            "stream" => true,
        ];

        header('Content-type: text/event-stream');
        header('Cache-Control: no-cache');

        $openAi->completion($opts, function ($curl_info, $data) {
            echo $data . "<br><br>";
            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });
    }
}
