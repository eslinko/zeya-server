<?php

namespace app\models;

class CurlHelper {
    static function curl($url, $data = [], $method = 'GET', $options = [])
    {
        $default_options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        if ($method === 'GET') {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= http_build_query($data);
        }
        if ($method === 'POST') {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        }
        if ($method === 'JSON') {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type:application/json';
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, array_replace($default_options, $options));

        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false) {
            throw new ErrorException("Curl error: ".curl_error($ch), curl_errno($ch));
        }

        return $result;
    }
}