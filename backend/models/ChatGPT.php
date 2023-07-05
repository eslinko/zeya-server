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

//        $userMessage = 'Привет! Как мне добраться до ближайшей кафе?';
//        $response = ChatGPT::sendChatGPTRequest($userMessage);
//        echo "<pre>";
//        var_dump($response);
//        echo "</pre>";
//        $assistantReply = $response['choices'][0]['message']['content'];
//        echo $assistantReply;
//        exit;
}