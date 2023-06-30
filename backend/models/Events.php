<?php

namespace app\models;

use DateTime;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Events".
 *
 * @property string $id
 * @property string $facebook_url
 * @property string $name
 * @property string $description
 * @property string $description_langs
 * @property string $raw_facebook_date
 * @property string $start_timestamp
 * @property string $end_timestamp
 * @property string $raw_facebook_place_image
 * @property string $place
 * @property string $address
 * @property string $facebook_category
 * @property string $ticket_url
 * @property string $organizer_facebook_title
 * @property string $organizer_id
 * @property string $status
 */
class Events extends ActiveRecord
{
    static $statuses = [
        'added' => 'ADDED',
        'opened' => 'OPENED',
        'in_processing' => 'IN_PROCESSING',
        'analysing' => 'ANALYSING',
        'processed' => 'PROCESSED',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['facebook_url'], 'required'],
          [['facebook_url'], 'url', 'message' => 'Please enter a valid Facebook Event URL. Ensure that the URL starts with the HTTPS protocol and has the correct format.'],
          [['facebook_url'], 'validateHttpsProtocol'],
          [['facebook_url'], 'unique'],

            [['ticket_url'], 'url', 'message' => 'Please enter a valid Facebook Event Ticket URL. Ensure that the URL starts with the HTTPS protocol and has the correct format.'],
            [['ticket_url'], 'validateHttpsProtocolForTicket'],

          [['facebook_url', 'place', 'raw_facebook_place_image', 'address', 'facebook_category', 'ticket_url', 'organizer_facebook_title'], 'string', 'max' => 255],
          [['name'], 'string', 'max' => 100],
          [['organizer_id', 'description_langs', 'start_timestamp', 'end_timestamp'], 'safe'],
          [['name', 'place', 'address', 'raw_facebook_place_image', 'facebook_category', 'ticket_url', 'status', 'raw_facebook_date'], 'string'],
         [['name', 'start_timestamp'],  function ($attribute, $params, $validator) {
             if (!empty($this->status) && $this->status !== 'added' && strlen(trim($this->$attribute)) < 1) {
                 $this->addError($attribute, $this->getAttributeLabel($attribute) . ' is required.');
             }
         }, 'skipOnEmpty' => false, 'skipOnError' => false],
         [['organizer_facebook_title', 'place', 'address', 'facebook_category', 'description'],  function ($attribute, $params, $validator) {
            if (in_array($this->status, ['in_processing', 'processed']) && strlen(trim($this->$attribute)) < 1) {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . ' is required.');
            }
         }, 'skipOnEmpty' => false, 'skipOnError' => false],
        [['description'], function ($attribute, $params, $validator) {
            $desc = $this->$attribute;
            if (in_array($this->status, ['in_processing', 'processed']) && empty($desc)) {
                $this->addError($attribute, 'Description is required.');
            } else if (strlen($desc) > 5000){
                $this->addError($attribute, 'The description must be no more than 5000 characters.');
            }
        }, 'skipOnEmpty' => false, 'skipOnError' => false],
            [['start_timestamp'], function($attribute, $params, $validator) {
                if(!empty($this->end_timestamp) && $this->start_timestamp >= $this->end_timestamp) {
                    $this->addError($attribute, $this->getAttributeLabel($attribute) . ' must be earlier than ' . $this->getAttributeLabel('end_timestamp'));
                }
            }, 'skipOnEmpty' => false, 'skipOnError' => false],
            [['start_timestamp', 'end_timestamp'], function($attribute, $params, $validator) {
                if($this->$attribute < 0) {
                    $this->$attribute = '';
                    $this->addError($attribute, 'Invalid start date format. Please enter the start date in the format: YYYY-MM-DD HH:MM');
                }
            }, 'skipOnEmpty' => false, 'skipOnError' => false]
        ];
    }

    public function validateHttpsProtocol($attribute, $params) {
        $url = $this->$attribute;
        if (!preg_match('/^https:\/\/.*/', $url)) {
            $this->addError($attribute, 'Please enter a valid Facebook Event Ticket URL. Ensure that the URL starts with the HTTPS protocol and has the correct format.');
        }
    }

    public function validateHttpsProtocolForTicket($attribute, $params) {
        $url = $this->$attribute;
        if (!preg_match('/^https:\/\/.*/', $url)) {
            $this->addError($attribute, 'Please enter a valid Facebook Event URL. Ensure that the URL starts with the HTTPS protocol and has the correct format.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'facebook_url' => 'Facebook URL*',
            'name' => 'Name*',
            'description' => 'Description',
            'description_langs' => 'Description Languages',
            'raw_facebook_date' => 'Data Raw from Facebook',
            'start_timestamp' => 'Start Time*',
            'end_timestamp' => 'End Time*',
            'raw_facebook_place_image' => 'Facebook Place Image',
            'place' => 'Place',
            'address' => 'Address',
            'facebook_category' => 'Category',
            'ticket_url' => 'Ticket URL',
            'status' => 'Status',
            'organizer_id' => 'Organiser ID (Internal)',
            'organizer_facebook_title' => 'Organiser Facebook Title',
        ];
    }
	
	static function filterEvents($params = []){
		$query = Events::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();

		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			switch ($param) {
				case 'organizer_id':
					$query = $query->andWhere(['in', 'organizer_id', $value]);
					break;
				case 'name':
				case 'facebook_url':
					$query = $query->andWhere(['like', $param, '%' . $value . '%', false]);
					break;
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}

		return $query;
	}

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            $this->status = self::setStatus($this);
            return true;
        } else {
            return false;
        }
    }

    static function setStatus ($model) {
        if(empty($model->status)) $status = 'added';
        else if (!empty($model->status) && $model->status === 'added') $status = 'opened';
        else $status = $model->status;

        if(!empty($model->name) && !empty($model->start_timestamp)) {
            $status = 'in_processing';
        }

        if(!empty($model->name) && !empty($model->start_timestamp) && !empty($model->organizer_facebook_title) && !empty($model->place) && !empty($model->address) && !empty($model->facebook_category) && !empty($model->description)) {
            $status = 'processed';
        }

        return $status;
    }

    static function parseDateStartAndDateEndByFacebookDateString($facebook_string, $return_format = 'timestamp') {
        $facebook_string = trim($facebook_string);
        if(strpos($facebook_string, 'AT') !== false) { // format 16 JUN AT 15:30 – 17 JUN AT 23:45
            $parts = explode(' – ', $facebook_string);
            $preDateStart = str_replace(' AT ', ' ', $parts[0]);
            $dateTimeStart = DateTime::createFromFormat('d M H:i', $preDateStart);
            $dateTimeStart = $dateTimeStart !== false ? $dateTimeStart : DateTime::createFromFormat('d M Y H:i', $preDateStart);

            if($dateTimeStart !== false) $dateTimeStart = $dateTimeStart->getTimestamp();

            $preDateEnd = str_replace('AT', '', $parts[1]);
            $dateTimeEnd = DateTime::createFromFormat('d M H:i', $preDateEnd);
            $dateTimeEnd = $dateTimeEnd !== false ? $dateTimeEnd : DateTime::createFromFormat('d M Y H:i', $preDateEnd);

            if($dateTimeEnd !== false) $dateTimeEnd = $dateTimeEnd->getTimestamp();
        } else { // format THURSDAY, 1 JUNE 2023 FROM 09:00-18:00
            // Разбиваем строку по разделителю "FROM"
            $parts = explode(" FROM ", $facebook_string);

            $dateString = $parts[0];

            // Получаем дату и время начала
            $times = trim($parts[1]);

            // Разбиваем строку с датой и временем начала по символу "-"
            $timesExplode = explode("-", $times);

            // Получаем дату начала
            $timeStart = trim($timesExplode[0]);

            // Получаем время начала
            $timeEnd = trim($timesExplode[1]);

            // Комбинируем дату и время в одну строку
            $dateTimeStart = $dateString . " " . $timeStart;
            $dateTimeStart = strtotime($dateTimeStart);
            $dateTimeEnd = $dateString . " " . $timeEnd;
            $dateTimeEnd = strtotime($dateTimeEnd);
        }

        if($return_format !== 'timestamp') {
            return ['date_start' => date($return_format, $dateTimeStart), 'date_end' => date($return_format, $dateTimeEnd)];
        }

        return ['date_start' => $dateTimeStart, 'date_end' => $dateTimeEnd];
    }
}
