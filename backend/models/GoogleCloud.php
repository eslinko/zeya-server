<?php

namespace app\models;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Yii;

class GoogleCloud {
    private $jsonFile = '/api_keys/' . GoogleVisionFile;
    private $googleMapApiKey = GoogleMapApiKey;

    public function __construct() {
        $this->jsonFile = Yii::getAlias('@webroot') . $this->jsonFile;
    }

    public function getTextFromImage($image_path, $return_type = 'string') {
        $client = new ImageAnnotatorClient([
            'credentials' => $this->jsonFile
        ]);

        $image = file_get_contents($image_path);
        $response = $client->documentTextDetection($image);
        $annotation = $response->getFullTextAnnotation();

//        $result = [];

        $words = [];
        $oneString = '';
        # print out detailed and structured information about document text
        if ($annotation) {
            foreach ($annotation->getPages() as $page) {
                foreach ($page->getBlocks() as $block) {
                    $block_text = '';
                    foreach ($block->getParagraphs() as $paragraph) {
                        foreach ($paragraph->getWords() as $word) {
                            $wordCompleted = '';
                            foreach ($word->getSymbols() as $symbol) {
                                $block_text .= $symbol->getText();
                                $wordCompleted .= $symbol->getText();
                            }
                            $block_text .= ' ';
                            $words[] = $wordCompleted;
                        }
                        $block_text .= "\n";
                    }
//                    $result .= $block_text;
                    $oneString .= $block_text;
                }
            }
        }

        $client->close();

        if($return_type === 'array') return $words;

        return $oneString;
    }

    public function getAdressObjectFromAdressString($address) {

        if(is_array($address)) {
            $res = $this->getAdressObjectFromAdressString(implode(' ', $address));

            if(!$res) {
                foreach ($address as $address_item) {
                    $res = $this->getAdressObjectFromAdressString($address_item);

                    if($res !== false) return $res;
                }
            }

            return $res;
        }

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $this->googleMapApiKey;

        $response = file_get_contents($url);
        $data = json_decode($response);
        $result = false;

        if ($data->status === "OK") {
//            $latitude = $data->results[0]->geometry->location->lat;
//            $longitude = $data->results[0]->geometry->location->lng;
            $result = $data->results[0];
        }

        return $result;
    }

    private function flash_encode($string)
    {
        $string = rawurlencode($string);

        $string = str_replace("%C2%96", "-", $string);
        $string = str_replace("%C2%91", "%27", $string);
        $string = str_replace("%C2%92", "%27", $string);
        $string = str_replace("%C2%82", "%27", $string);
        $string = str_replace("%C2%93", "%22", $string);
        $string = str_replace("%C2%94", "%22", $string);
        $string = str_replace("%C2%84", "%22", $string);
        $string = str_replace("%C2%8B", "%C2%AB", $string);
        $string = str_replace("%C2%9B", "%C2%BB", $string);

        return $string;
    }
}