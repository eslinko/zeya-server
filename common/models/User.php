<?php
namespace common\models;

use app\models\User2Teacher;
use app\models\User2Partner;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $full_name
 * @property string $email
 * @property string $temp_email
 * @property string $telegram
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
            [['status'], 'integer'],
            [['created_at', 'updated_at', 'currentLovestarsCounter', 'partners', 'verificationCode', 'teacher', 'temp_email', 'language', 'invitation_code_id', 'calculated_interests', 'interests_description', 'last_request_to_chatgpt_date'], 'safe'],
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
		return $user->save();
	}

    static function getArrWithIdLabel($users_arr) {
        $res = [];
        foreach ($users_arr as $user_item) {
            $label = '';
            if(!empty($user_item['username'])) $label = $user_item['username'];
            else if(!empty($user_item['publicAlias'])) $label = $user_item['publicAlias'];
            else if(!empty($user_item['full_name'])) $label = $user_item['full_name'];
            $label .= ' | ID:' . $user_item['id'];
            $res[$user_item['id']] = $label;
        }
        return $res;
    }
}
