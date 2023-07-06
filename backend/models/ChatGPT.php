<?php

namespace app\models;

class ChatGPT {
    static function sendChatGPTRequest($message) {
        $apiKey = ChatGPTApiKey;
        $url = 'https://api.openai.com/v1/chat/completions';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ];

        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    static function getUserInterests($user_text) {
        $message = "Take this text that comes from a community member describing himself/herself and extract a detailed list of interests and values, in English. Make this list comma-separated. \n";
        $message .= $user_text;
        $response = self::sendChatGPTRequest($message);

        return !empty($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : false;
    }

//$userMessage = "Take this text that comes from a community member describing himself/herself and extract a detailed list of interests and values, in English. Make this list comma-separated. \n
//            Мне нравится компьютерные игры, заниматься спортом, иногда я очень громко слушаю музыку и смотрю фильмы для взрослых";
//$response = ChatGPT::sendChatGPTRequest($userMessage);
//echo "<pre>";
//var_dump($response);
//echo "</pre>";
//$assistantReply = $response['choices'][0]['message']['content'];
//echo $assistantReply;
//exit;
}