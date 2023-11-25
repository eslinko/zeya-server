<?php
namespace common\models;

use app\models\CreativeExpressions;
use app\models\InvitationCodes;
use app\models\Lovestar;
use app\models\MatchAction;
use app\models\Matches;
use app\models\Notifications;
use app\models\PartnerRuleAction;
use app\models\User2Teacher;
use app\models\User2Partner;
use app\models\UserInterestsAnswers;
use backend\models\UserConnections;
use backend\models\UsersWithSharedInterests;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use \yii\helpers\FileHelper;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $full_name
 * @property string $email
 * @property string $temp_email
 * @property string $telegram
 * @property string $telegram_alias
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $role
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $currentLovestarsCounter
 * @property boolean $verifiedUser
 * @property string $verificationCode
 * @property string $invitation_code_id
 * @property string $publicAlias
 * @property string $language
 * @property string $calculated_interests
 * @property string $interests_description
 * @property string $last_request_to_chatgpt_date
 * @property boolean $notify_connections
 * @property boolean $notify_matches
 * @property boolean $notify_invite_codes
 * @property boolean $notify_ce_activity
 * @property string $last_notification_read_time
 * @property integer $message_counter
 * @property string $profile_data
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const ROLES = [
        'user' => 'User',
        'admin' => 'Administrator',
        'event_processor' => 'Event Processor',
        'event_organizer' => 'Event Organizer',
    ];

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_EVENT_PROCESSOR = 'event_processor';
    const ROLE_EVENT_ORGANIZER = 'event_organizer';

    public $confirmPass;
	
	public $teacher;
	public $partners;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%User}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'password_hash', 'role', 'status', 'full_name', 'telegram', 'verifiedUser', 'publicAlias'], 'required'],
            [['status','message_counter'], 'integer'],
            [['created_at', 'updated_at', 'currentLovestarsCounter', 'partners', 'verificationCode', 'teacher', 'temp_email', 'language', 'invitation_code_id', 'calculated_interests', 'interests_description', 'last_request_to_chatgpt_date','telegram_alias','notify_connections','notify_matches','notify_invite_codes','notify_ce_activity','last_notification_read_time'], 'safe'],
            [['username', 'password_hash', 'full_name', 'email', 'confirmPass'], 'string', 'max' => 255],
            [['password_hash', 'confirmPass'], 'string', 'min' => 5],
            [['role'], 'string', 'max' => 32],
//            [['teacher'], 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'telegram', 'publicAlias', 'username'], 'unique'],
            ['confirmPass', 'compare', 'compareAttribute' => 'password_hash', 'message' => 'The fields "Password confirmation" and "Password" must match'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'full_name' => 'Full name',
            'email' => 'E-mail',
            'temp_email' => 'Temp E-mail',
            'password_hash' => 'Password',
            'role' => 'Role',
            'id' => 'ID',
            'status' => 'Status',
            'confirmPass' => 'Password confirmation',
            'currentLovestarsCounter' => 'Current Lovestars',
            'teacher' => 'Teacher',
            'partners' => 'Partners',
            'verifiedUser' => 'Verified User',
            'publicAlias' => 'Public Alias',
            'language' => 'Language',
            'calculated_interests' => 'Calculated interests',
            'interests_description' => 'Interests description',
            'telegram_alias' => 'Telegram alias',
            'message_counter' => 'Message counter',
            'profile_data' => 'Profile data(json)'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by role
     *
     * @param string $role
     * @return static|null
     */
    public static function findByRole($role)
    {
        return static::findOne(['role' => $role, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

//    public function beforeSave($insert)
//    {
//        if(parent::beforeSave($insert)){
//            if(empty($this->password_hash)) {
//                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password_hash);
//            }
//            return true;
//        }else{
//            return false;
//        }
//    }
	
	static function filterUsers($params = []){
		$query = User::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort') continue;
			
			switch ($param) {
				case 'username':
				case 'email':
				case 'full_name':
					$query = $query->andWhere(['like', $param, '%' . $value . '%', false]);
					break;
				case 'teacher':
					$user_ids = array_column(User2Teacher::find()->where(['teacherId' => $value])->all(), 'userId');
					$user_ids = !empty($user_ids) ? $user_ids : [0];
					$query = $query->andWhere(['in', 'id', $user_ids]);
					break;
				case 'partner':
					$user_ids = array_column(User2Partner::find()->where(['partnerId' => $value])->all(), 'userId');
					$user_ids = !empty($user_ids) ? $user_ids : [0];
					$query = $query->andWhere(['in', 'id', $user_ids]);
					break;
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}
		
		return $query;
	}
	
	static function addedLovestarsCount($user_id, $count) {
		$user = User::findOne($user_id);
		if(empty($user)) return ['status' => false, 'message' => 'User by ID not found.'];

		$user->currentLovestarsCounter = (int) $user->currentLovestarsCounter + (int) $count;
        return $user->save(false);
	}

    static function getArrWithIdLabel($users_arr) {
        $res = [];
        foreach ($users_arr as $user_item) {
            if(empty($user_item['id'])) continue;
            $label = '';
            if(!empty($user_item['username'])) $label = $user_item['username'];
            else if(!empty($user_item['publicAlias'])) $label = $user_item['publicAlias'];
            else if(!empty($user_item['full_name'])) $label = $user_item['full_name'];
            $label .= ' | ID:' . $user_item['id'];
            $res[$user_item['id']] = $label;
        }
        return $res;
    }

    static function calculatedInterestsToList($calculated_interests) {
        $arr = explode(',', $calculated_interests);
        $res = "";
        foreach ($arr as $i => $item) {
            $res .= ($i + 1). ". ". trim($item) . "\n";
        }
        return $res;
    }
    public static function setTelegramAlias($telegram_id, $alias)
    {
        $user=User::find()->where(['telegram' => $telegram_id])->one();
        if($user->telegram_alias!==$alias){//user might change telegram username
            $user->telegram_alias=$alias;
            $user->save(false);
        }
    }
    public static function setMessageCounter($user_id, $counter) {
        $user=User::find()->where(['id' => $user_id])->one();
        $user->message_counter = intval($counter);
        if($user->save(false))
            return true;
        else
            return false;
    }
    public static function MessageCounterIncrement($user_id) {
        $user=User::find()->where(['id' => $user_id])->one();
        $user->message_counter = $user->message_counter + 1;
        $user->save(false);
    }
    public static function setProfileData($user_id, $field, $value) {
        $user=User::find()->where(['id' => $user_id])->one();
        if(empty($user->profile_data)){
            $profile_data = [$field => $value];
        } else {
            $profile_data = json_decode($user->profile_data, true);
            if($profile_data === NULL) $profile_data = [];
            $profile_data[$field] = $value;
        }
        $user->profile_data = json_encode($profile_data);
        $user->save(false);
        return true;
    }
    public static function uploadAvatarFromTelegram($user_id, $file_id)
    {
        $supported_formats = ['jpg','jpeg','png','gif'];
        $target_dir = Yii::getAlias('@webroot').'/uploads/avatars/';

        if(!file_exists($target_dir)){
            FileHelper::createDirectory($target_dir);
        }

        // get file path
        $url = "https://api.telegram.org/bot".TelegramBotId."/getFile?file_id={$file_id}";
        $result = json_decode(CurlHelper::curl($url));

        if(!$result->ok) {
            return false;
        }

        $file_path = $result->result->file_path;

        if(empty($file_path)) {
            return false;
        }
        $arr = explode('.', $file_path);
        $ext = strtolower($arr[count($arr)-1]);
        if(!in_array($ext,$supported_formats)) return 'unsupported_format';


        //get file
        $url = "https://api.telegram.org/file/bot".TelegramBotId."/{$file_path}";
        $file = file_get_contents($url);

        if(!$file) {
            return false;
        }

        $new_file_name = uniqid() . '_' . basename($url);

        $target_file = $target_dir . $new_file_name;

        if (file_put_contents($target_file, $file) !== false) {
            return $new_file_name;
        } else {
            return false;
        }
    }
    public static function kill($user_id){
        //delete user from database

        //delete invitation codes
        $data = InvitationCodes::find()->where(['user_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error invitation codes';
        }
        $data = InvitationCodes::find()->where(['registered_user_id' => $user_id])->one();
        if($data !== NULL){
            $data->registered_user_id = NULL;
            $data->save(false);
        }

        //del notifications
        $data = Notifications::find()->where(['user_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error notifications';
        }
        $data = Notifications::find()->where(['related_entity_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error notifications';
        }

        //delete creative expressions
        $data = CreativeExpressions::find()->where(['user_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error creative expressions';
        }

        //delete lovestar emittedLovestarsUser
        $data = Lovestar::find()->where(['currentOwner' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error lovestar';
        }

        //delete partnerruleaction
        $data = PartnerRuleAction::find()->where(['emittedLovestarsUser' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error partnerruleaction';
        }

        //delete matchactions
        $data = MatchAction::find()->where(['action_user_id' => $user_id])->orWhere(['expression_user_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error matchactions';
        }

        //delete matches
        $data = Matches::find()->where(['user_1_id' => $user_id])->orWhere(['user_2_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error matches';
        }

        //connections
        $data = UserConnections::find()->where(['user_id_1' => $user_id])->orWhere(['user_id_2' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error connections';
        }

        //interests
        $data = UserInterestsAnswers::find()->where(['user_id' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'error interests';
        }
        //shared interests
        $data = UsersWithSharedInterests::find()->where(['user_id_1' => $user_id])->orWhere(['user_id_2' => $user_id])->all();
        foreach ($data as $dat) {
            if($dat->delete() === false)
                return 'shared interests';
        }

        $data = User::find()->where(['id' => $user_id])->one()->delete();
        if($data === false)
            return 'error user';
        else
            return true;
    }
    public static function getUsersInterestsMatchPercentage($user_id_1, $user_id_2){
        $user_1 = User::find()->where(['id' => $user_id_1])->one();
        $user_2 = User::find()->where(['id' => $user_id_2])->one();
        $user_1_interests = unserialize($user_1->calculated_interests);
        $user_2_interests = unserialize($user_2->calculated_interests);
        if(!isset($user_1_interests['en']) OR !isset($user_2_interests['en'])) return 0;
        $user_1_count = count($user_1_interests['en']);
        if($user_1_count == 0) return 0;
        $match_count = 0;
        foreach ($user_1_interests['en'] as $interest){
            if(in_array($interest, $user_2_interests['en']))
                $match_count++;
        }
        $one_perc = $user_1_count/100;
        return $match_count/$one_perc;
    }
}
