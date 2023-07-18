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
        $message = "Take this text that comes from a community member describing himself/herself and extract a detailed list of interests and values, in English, even if this text consists of a single word. Make this list comma-separated and return without any another symbols and text. Create a list even if it's a question. List: \n";
        $message .= $user_text;
        $response = self::sendChatGPTRequest($message);

        return !empty($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : false;
    }

    static function calculatedInterestsToListByLang($calculated_interests, $lang = 'en') {
        $message = "Take this list, translate it into " . $lang . " and display as numbered list, and no unnecessary text other than the list. Don't divide it into interests and values, just put it all in one list : \n";
        $message .= $calculated_interests;
        $response = self::sendChatGPTRequest($message);

        return !empty($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : false;
    }

    static function translateNewItemOnAllLanguages($new_item, $languages) {
        $languages_formatted = [];
        foreach ($languages as $lang) {
            $languages_formatted[] = self::languageCompareForBot($lang);
        }
        $message = "Translate this text into " . implode(',', $languages_formatted) . " languages: '{$new_item}', then return it without any another symbols and text and with format: language=translate,language=translate,language=translate";
        $response = self::sendChatGPTRequest($message);

        if(!empty($response['choices'][0]['message']['content'])) {
            $arr = explode(',', $response['choices'][0]['message']['content']);
            $res = [];
            foreach ($arr as $item) {
                $arr_item = explode('=', $item);
                $res[self::languageCompareForBotInversely($arr_item[0])] = $arr_item[1];
            }

            return $res;
        } else {
            return false;
        }
    }

    static function removeInterestFromUserList($calculated_interests, $number_of_list) {
        $message = "Take this list '{$calculated_interests}' display as numbered list and delete number {$number_of_list} from it. Then separate the list with commas and return it to me without any extra text";
        $response = self::sendChatGPTRequest($message);

        return !empty($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : false;
    }

    static function translateCalculatedInterest($calculated_interests, $lang = 'en') {
        $message = "Take this list, translate it into " . self::languageCompareForBot($lang) . " and return back: \n";
        $message .= $calculated_interests;
        $response = self::sendChatGPTRequest($message);

        return !empty($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : false;
    }

    static function languageCompareForBot($lang){
        switch ($lang){
            case 'et':
                $return = 'estonian';
                break;
            default:
                $return = $lang;
                break;
        }
        return $return;
    }

    static function languageCompareForBotInversely($lang){
        switch ($lang){
            case 'estonian':
                $return = 'et';
                break;
            default:
                $return = $lang;
                break;
        }

        return $return;
    }

}