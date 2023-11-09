<?php

namespace backend\controllers;

use app\models\CreativeExpressions;
use app\models\CreativeTypes;
use app\models\InvitationCodesLogs;
use app\models\Lovestar;
use app\models\MatchAction;
use app\models\Matches;
use app\models\Notifications;
use app\models\Partner;
use app\models\PartnerRule;
use app\models\PartnerRuleAction;
use app\models\Settings;
use app\models\UserInterestsAnswers;
use backend\models\ChatGPT;
use app\models\Events;
use app\models\InvitationCodes;
use app\models\Languages;
use app\models\SendGridMailer;
use app\models\Teacher;
use app\models\HashTag;
use backend\models\UsersWithSharedInterests;
use common\models\CurlHelper;
use common\models\TelegramApi;
use app\models\TelegramChatsLastMessage;
use app\models\User2Teacher;
use backend\models\UserConnections;
use backend\models\EmailSendVerificationCode;
use common\models\User;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class TelegramApiController extends AppController
{
    /**
     * @inheritdoc
     */
/*    public function init() {
        //$this->enableCsrfValidation = false;//enable incoming POST requests
    }*/
/*    public function actions() {
        return [
            'notifications' => [
                'class' => 'yii\web\UrlRule',
                'pattern' => 'telegram-api/notifications/<messageId:\d+>/read',
                'route' => 'telegram-api/notifications'
            ]
        ];
    }*/
    public function beforeAction($action) { //enable incoming POST requests
        $post_actions = ['notifications-delete','notifications-read','notifications-read-all','set-text-content-to-expression'];
        if(in_array($action->id, $post_actions)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'allow' => true,
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//        ];
//    }

    public function actionGetUserByTelegramId()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->asArray()->one();

        $result = [];
        if (empty($user)) {
            $user = new User();
            $user->telegram = $data['telegram_id'];
            $user->status = 10;
            $user->verificationCode = strtoupper(substr(md5(microtime()), rand(0, 26), 3) . '-' . substr(md5(microtime()), rand(0, 26), 3) . '-' . substr(md5(microtime()), rand(0, 26), 3));
            $user->save(false);
        } else $result['status'] = 'success';

        // set default language if we can
        $cur_user_lang = !empty($user->language) ? $user->language : (!empty($user['language']) ? $user['language'] : '');
        if (!empty($data['telegram_language_code']) && empty($cur_user_lang)) {
            $lang = Languages::find()->where(['code' => $data['telegram_language_code']])->one();
            if (!empty($lang) && $lang->status === 'active') {
                if (is_array($user)) {
                    $userObj = User::find()->where(['id' => $user['id']])->one();
                    $userObj->language = $lang->code;
                    $userObj->save(false);
                    $user['language'] = $lang->code;
                } else {
                    $user->language = $lang->code;
                    $user->save(false);
                }
            } else {
                $new_lang = new Languages();
                $new_lang->title = !empty(Languages::$languages_list[$data['telegram_language_code']]) ? Languages::$languages_list[$data['telegram_language_code']] : $data['telegram_language_code'];
                $new_lang->code = $data['telegram_language_code'];
                $new_lang->status = 'untranslated';
                $new_lang->save();
                $telegram_id = is_array($user) ? $user['telegram'] : $user->telegram;
                $admins = User::find()->where(['role' => 'admin'])->all();
                TelegramApi::sendNotificationToUsersTelegram("Alarm! New language detected during new user registration! Title: {$new_lang->title}, code: {$new_lang->code}, user telegram ID: $telegram_id", $admins);
            }
        }

        $result['user'] = $user;
        if (is_array($user)) {
            $user_id = $user['id'];
        } else {
            $user_id = $user->id;
        }

        $creative_expression = CreativeExpressions::find()
            ->where(['user_id' => $user_id])
            ->andWhere(['status' => 'process_of_creation'])
            ->asArray()
            ->one();

/*        if(!empty($creative_expression['type'])) {//DEPRECATED
            $creative_expression['type_names'] = CreativeTypes::find($creative_expression['type'])
                ->where(['id' => $creative_expression['type']])
                ->asArray()
                ->one();
        }*/
        if($creative_expression!== NULL AND !empty($creative_expression['content']) AND mb_strlen($creative_expression['content'])>500) {
            $creative_expression['content'] = mb_substr($creative_expression['content'],0,497).'...';
        }
        $result['expressions_in_proccess'] = $creative_expression;

        return $result;
    }

    public function actionGetUserLastMessage()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->asArray()->one();
        $user = empty($user) ? [] : $user;

        return ['status' => 'success', 'message' => TelegramChatsLastMessage::getLastMessage($data['telegram_id']), 'user' => $user];
    }

    public function actionSetUserLastMessage()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        if (empty(TelegramChatsLastMessage::getLastMessage($data['telegram_id']))) {
            TelegramChatsLastMessage::createLastMessage($data['telegram_id'], $data['message']);
        } else {
            TelegramChatsLastMessage::updateLastMessage($data['telegram_id'], $data['message']);
        }
        $message=json_decode($data['message'],true);
        if(isset($message['chat']['username'])){
            //if user did not set up username, then username field is missing
            User::setTelegramAlias($data['telegram_id'],$message['chat']['username']);
        }

        return ['status' => 'success', 'user' => User::find()->where(['telegram' => $data['telegram_id']])->one()];
    }

    public function actionSetUserEmail()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user_with_email = User::find()->where(['email' => $data['email']])->one();

        if (!empty($user_with_email)) {
            return ['status' => 'user_with_email_exist', 'user' => $user_with_email];
        }

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $user->email = $data['email'];
        $user->username = $data['email'];
        $user->save(false);

        return ['status' => 'success'];
    }

    public function actionSetPublicAlias()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        $usersWithPublicAlias = User::find()->where(['publicAlias' => $data['publicAlias']])
            ->orWhere(['username' => $data['publicAlias']])
            ->exists();

        if (empty($user)) return ['status' => 'error'];
        if ($usersWithPublicAlias) return ['status' => 'error', 'type' => 'user_with_publicalias_exist', 'user' => $user];

        $user->publicAlias = $data['publicAlias'];
        $user->full_name = $data['publicAlias'];
        $user->username = $data['publicAlias'];
        if($user->save(false))
            return ['status' => 'success', 'user' => $user];
        else
            return ['status' => 'error', 'user' => $user];
    }

    public function actionSetUserPassword()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $password = base64_decode(urldecode($data['password']));
        $user->setPassword($password);
        $user->save(false);

        return ['status' => 'success', 'user' => $user];
    }

    public function actionSendVerificationEmail()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $model = new EmailSendVerificationCode();
        $model->sendEmail($user);

        return ['status' => 'success', 'user' => $user];
    }

    public function actionSetUserVerified()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error', 'user' => $user];

        if ((string)$user->verificationCode !== (string)$data['code']) {
            return ['status' => 'wrong_code', 'user' => $user];
        }

        $user->verifiedUser = '1';
        $user->save();

        return ['status' => 'success', 'user' => $user];
    }

    public function actionBecomeTeacher()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $teacher = new Teacher();
        $teacher->title = $data['teacher_title'];
        $teacher->save();

        User2Teacher::connectTeacherToUser($user->id, $teacher->id);

        $telegramData = TelegramChatsLastMessage::getLastMessage($data['telegram_id']);
        $telegramData->active_teacher_id = $teacher->id;
        $telegramData->save();

        return ['status' => 'success', 'teacher_title' => $teacher->title];
    }

    public function actionUpdateTeacher()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();
        if (empty($user)) return ['status' => 'error', 'text' => ''];

        //get active teacher
        $telegramData = TelegramChatsLastMessage::getLastMessage($data['telegram_id']);
        $teacher = Teacher::find()->where(['id' => $telegramData->active_teacher_id])->andWhere(['status' => 'active'])->one();

        if (empty($teacher)) {
            return ['status' => 'error', 'text' => 'There is no active teacher.'];
        }

        if (!empty($data['teacher_title'])) {
            $teacher->title = $data['teacher_title'];
        }

        if (!empty($data['teacher_public_alias'])) {
            $teacher->publicAlias = $data['teacher_public_alias'];
        }

        if (!empty($data['teacher_description'])) {
            $teacher->description = $data['teacher_description'];
        }

        if (!empty($data['teacher_hashtags'])) {
            $hashtags = HashTag::fromArrayToHashtags(explode(',', $data['teacher_hashtags']));
            $teacher->hashtags = implode(',', $hashtags);
        }

        $teacher->save();

        return ['status' => 'success', 'teacher' => $teacher];
    }

    public function actionGetTeachers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $teachers = User2Teacher::getAllTeachersByUserId($user->id, 'active');
        $res = [];
        foreach ($teachers as $key => $teacher) {
            $res[$key] = $teacher;
            $res[$key]['hashtags'] = HashTag::fromIdsToNames($teacher['hashtags']);
        }

        return ['status' => 'success', 'teachers' => json_encode($res)];
    }

    public function actionSetActiveTeacher()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $teachers = User2Teacher::getAllTeachersByUserId($user->id);

        $selectedCol = [];

        foreach ($teachers as $teacher) {
            if ($teacher['publicAlias'] == $data['teacher_public_alias']) {
                $selectedCol = $teacher;
                break;
            }
        }

        if (empty($selectedCol)) {
            return ['status' => 'error', 'text' => 'There is no such teacher.'];
        }

        $telegramData = TelegramChatsLastMessage::getLastMessage($data['telegram_id']);
        $telegramData->active_teacher_id = $selectedCol['id'];
        $telegramData->save();

        return ['status' => 'success', 'teacher' => $selectedCol];
    }

    public function actionGetActiveTeacher()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $telegramData = TelegramChatsLastMessage::getLastMessage($data['telegram_id']);
        $teacher = Teacher::find()->where(['id' => $telegramData->active_teacher_id])->andWhere(['status' => 'active'])->one();

        if (empty($teacher)) {
            return ['status' => 'error', 'text' => 'There is no active teacher.'];
        }

        return ['status' => 'success', 'teacher' => $teacher];
    }

    public function actionAssignTeacherToUser()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();
        if (empty($user)) return ['status' => 'error', 'text' => ''];

        $userToAssign = User::find()->where(['publicAlias' => $data['user_public_alias']])->one();
        if (empty($userToAssign)) return ['status' => 'error', 'text' => 'The user with this public alias does not exist.'];

        //get active teacher
        $telegramData = TelegramChatsLastMessage::getLastMessage($data['telegram_id']);
        $teacher = Teacher::find()->where(['id' => $telegramData->active_teacher_id])->andWhere(['status' => 'active'])->one();

        if (empty($teacher)) {
            return ['status' => 'error', 'text' => 'There is no active teacher.'];
        }

        User2Teacher::connectTeacherToUser($userToAssign->id, $telegramData->active_teacher_id);

        return ['status' => 'success', 'text' => 'The assignment was successful.'];
    }

    public function actionArchiveTeacher()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();
        if (empty($user)) return ['status' => 'error', 'text' => ''];

        //get active teacher
        $telegramData = TelegramChatsLastMessage::getLastMessage($data['telegram_id']);
        $teacher = Teacher::find()->where(['id' => $telegramData->active_teacher_id])->one();

        if (empty($teacher)) {
            return ['status' => 'error', 'text' => 'There is no active teacher.'];
        }

        $teacher->status = 'archive';

        $teacher->save();

        return ['status' => 'success', 'text' => 'Teacher successfully deleted.', 'teacher' => $teacher];
    }

    public function actionGetCodeForNewEmail()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user_with_email = User::find()->where(['email' => $data['email']])->one();

        if (!empty($user_with_email)) {
            return ['status' => 'user_with_email_exist', 'user' => $user_with_email];
        }

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $user->verificationCode = strtoupper(substr(md5(microtime()), rand(0, 26), 3) . '-' . substr(md5(microtime()), rand(0, 26), 3) . '-' . substr(md5(microtime()), rand(0, 26), 3));
        $user->temp_email = $data['email'];
        $user->save(false);

        $model = new EmailSendVerificationCode();
        $model->sendEmail($user, $data['email']);

//        $sendgrid = new SendGridMailer();
//        $sendgrid->sendEmail($data['email'], 'Verification From LovestarBot', 'test content');
//        $sendgrid->sendEmail($data['email'], 'Verification From LovestarBot', 'test content');

        return ['status' => 'success'];
    }

    public function actionUpdateUserEmail()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        if ((string)$user->verificationCode !== (string)$data['code']) {
            return ['status' => 'wrong_code', 'user' => $user];
        }

        $user->email = $user->temp_email;
        $user->username = $user->temp_email;
        $user->verifiedUser = '1';
        $user->save(false);

        return ['status' => 'success', 'user' => $user];
    }

    public function actionEventsUrlAdd()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error'];

        $events_url = explode("\n", $data['events_url']);
        $resultOfEvents = [];
        foreach ($events_url as $item) {
            $explodeByComma = explode(',', $item);
            foreach ($explodeByComma as $commaItem) {
                if (!empty(trim($commaItem))) {
                    $commaItem = trim($commaItem);
                    $resultOfEvents[] = $commaItem;
                }
            }
        }

        $result = [];
        foreach ($resultOfEvents as $event_url) {
            if (filter_var($event_url, FILTER_VALIDATE_URL) !== false && preg_match("~^(?:f|ht)tps?://~i", $event_url)) {
                if (Events::find()->where(['facebook_url' => $event_url])->exists()) {
                    $result['url_already_exist'][] = $event_url;
                } else {
                    $new_event = new Events();
                    $new_event->facebook_url = $event_url;
                    if ($user->role === 'event_organizer') {
                        $new_event->organizer_id = $user->id;
                    }
                    $new_event->save(false);
                    $result['success'][] = $event_url;
                }
            } else {
                $result['not_correct_url'][] = $event_url;
            }
        }

        return ['status' => 'success', 'events_result' => $result, 'user' => $user];
    }

    public function actionGetMyEvents()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error'];

        $events = Events::find()->where(['organizer_id' => $user->id])->all();

        return ['status' => 'success', 'events' => $events];
    }

    public function actionSetUserLanguage()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $data['telegram_id']])->one();

        if (empty($user)) return ['status' => 'error'];

        $user->language = $data['language'];
        $user->save(false);

        return ['status' => 'success', 'user' => $user];
    }

    public function actionGetActiveLanguages()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error'];

        return ['status' => 'success', 'user' => $user, 'languages' => Languages::find()->where(['status' => 'active'])->all()];
    }

    public function actionSendNotificationToAdmin()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error'];

        $user_name = 'unknown user(telegram id: '.$user->telegram.')';
        if(!empty($user->publicAlias))
            $user_name = $user->publicAlias;
        if(!empty($user->telegram_alias))
            $user_name = '@'.$user->telegram_alias;
        if(!empty($user->telegram_alias) AND !empty($user->publicAlias))
            $user_name = $user->publicAlias.' (@'.$user->telegram_alias.')';
        $message = str_replace('{userPublicAlias}', $user_name , $data['message']);

        $admins = User::find()->where(['role' => 'admin'])->all();
        TelegramApi::sendNotificationToUsersTelegram($message, $admins);

        return ['status' => 'true', 'user' => $user];
    }
    public function actionGenerateCodes()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (empty($data)) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        if (empty($data['amount']) OR is_numeric($data['amount']) == false) return ['status' => 'error', 'text' => 'This amount is not valid'];

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        if($user['role'] !== 'admin') return ['status' => 'error', 'text' => 'Error! Try again later.'];
        $owner_user = User::find()->where(['publicAlias' => $data['alias']])->asArray()->one();
        if($owner_user === NULL) return ['status' => 'error', 'text' => 'User not found'];
        InvitationCodes::generateCodes($owner_user['id'], intval($data['amount']));
        return ['status' => 'success', 'owner_user' => $owner_user];
    }
    public function actionSetInvitationCode()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (empty($data)) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        if (empty($data['code'])) return ['status' => 'error', 'text' => 'This code is not valid or has already been redeemed'];

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $result = InvitationCodes::useCodeForInvitation($data['code'], $user->id);
        if($result['status'] === 'success') {
            $code_owner = InvitationCodes::getInvitationCodeOwnerUserId($data['code']);
            //give Lovestar
            if(!empty($code_owner)) {
                //PartnerRuleAction::createAction(2,$code_owner);
                PartnerRuleAction::actionRegistrationGivesCodeOwnerLovestar($code_owner);
                //file_put_contents('log.txt',"1\n",FILE_APPEND);
                if(Settings::GiveLovestarViaConnections()==true){
                    $res = PartnerRuleAction::actionRegistrationGivesLovestarToCodeOwnerConnections($code_owner);
                    $result['code_owner_connections'] = $res;
                }
                //create connection
                UserConnections::setUserConnection($code_owner, $user->id, 'accepted');
                //find owner user
                $result['owner_user'] = User::findOne($code_owner);
                Notifications::createNotification(Notifications::INVITE_CODE_USED, $user, $result['owner_user']);
                foreach ($result['code_owner_connections'] as $code_owner_connection) {
                    Notifications::createNotification(Notifications::INVITE_CODE_USED_CONNECTIONS, $user, $code_owner_connection, $result['owner_user']);//from registered user to owner connections via owner user

                }
            }
        }
        return $result;
    }

    public function actionGetMyInvitationCodes()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'codes' => InvitationCodes::getUserInvitationCodes($user->id)];
    }
    public function actionGetMyNotUsedInvitationCodes()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'codes' => InvitationCodes::getUserNotUsedInvitationCodes($user->id)];
    }

    public function actionSetUserInterests()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user && !empty($data['entered_text'])) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $interests_description = $data['entered_text'];
        $calculated_interests = ['en' => ChatGPT::getUserInterests($interests_description)];
        if($data['user_lang'] === 'en' || empty($data['user_lang'])) {
            $list_of_interests = User::calculatedInterestsToList($calculated_interests['en']);
        } else {
            $calculated_interests[$data['user_lang']] = ChatGPT::translateCalculatedInterest($calculated_interests['en'], $data['user_lang']);
            $list_of_interests = User::calculatedInterestsToList($calculated_interests[$data['user_lang']]);
        }

        $user->calculated_interests = serialize($calculated_interests);
        $user->interests_description = json_decode($interests_description);
        $user->save(false);

        UsersWithSharedInterests::setNeedUpdateSharedInterests($user->id);

        return ['status' => 'success', 'list_of_interests' => $list_of_interests];
    }
    public function actionSetUserInterestsAnswers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if ($user === false) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        $interests_answ_array = UserInterestsAnswers::find()->where(['user_id' => $user->id])->asArray()->all();
        if ($interests_answ_array === NULL) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        $interests_answers_text = '';
        foreach ($interests_answ_array as $answ){
            $interests_answers_text.=$answ['response']."\n";
        }
        if (empty($interests_answers_text)) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $interests_description = $interests_answers_text;
        $calculated_interests = ['en' => ChatGPT::getUserInterests2($interests_description)];
        if($data['user_lang'] === 'en' || empty($data['user_lang'])) {
            $list_of_interests = User::calculatedInterestsToList($calculated_interests['en']);
        } else {
            $calculated_interests[$data['user_lang']] = ChatGPT::translateCalculatedInterest($calculated_interests['en'], $data['user_lang']);
            $list_of_interests = User::calculatedInterestsToList($calculated_interests[$data['user_lang']]);
        }

        $user->calculated_interests = serialize($calculated_interests);
        //$user->interests_description = json_decode($interests_description);
        $user->save(false);

        UsersWithSharedInterests::setNeedUpdateSharedInterests($user->id);

        return ['status' => 'success', 'list_of_interests' => $list_of_interests];
    }

    public function actionGetUserInterestsList()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user && !empty($data['entered_text'])) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $calculated_interests = unserialize($user->calculated_interests);

        if(empty($calculated_interests['en'])) {
            $list_of_interests = '';
        } else if(!empty($data['user_lang']) && !empty($calculated_interests[$data['user_lang']])) {
            $list_of_interests = User::calculatedInterestsToList($calculated_interests[$data['user_lang']]);
        } else if(!empty($data['user_lang'])) {
            $calculated_interests[$data['user_lang']] = ChatGPT::translateCalculatedInterest($calculated_interests['en'], $data['user_lang']);
            $list_of_interests = User::calculatedInterestsToList($calculated_interests[$data['user_lang']]);
            $user->calculated_interests = serialize($calculated_interests);
        } else {
            $list_of_interests = User::calculatedInterestsToList($calculated_interests['en']);
        }

        $user->last_request_to_chatgpt_date = time();
        $user->save(false);

        return ['status' => 'success', 'list_of_interests' => $list_of_interests];
    }

    public function actionAddInterestToUserList()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user && !empty($data['entered_text'])) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $calculated_interests = unserialize($user->calculated_interests);

        $calculated_interests_languages = array_keys($calculated_interests);

        $new_interest_translates = ChatGPT::translateNewItemOnAllLanguages($data['entered_text'], $calculated_interests_languages);

        foreach ($new_interest_translates as $lang => $new_item) {
            if(!empty($calculated_interests[$lang]) && $lang !== $data['user_lang']) {
                $calculated_interests[$lang] .= ',' . $new_item;
            }
        }

        $calculated_interests[$data['user_lang']] .= ',' . $data['entered_text'];
        $user->calculated_interests = serialize($calculated_interests);
        $user->save(false);

        UsersWithSharedInterests::setNeedUpdateSharedInterests($user->id);

        return ['status' => 'success'];
    }

    public function actionRemoveInterestFromUserList()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user && !empty($data['number_to_remove'])) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $calculated_interests = unserialize($user->calculated_interests);
        $key = ((int) $data['number_to_remove'] - 1);

        foreach ($calculated_interests as $lang => $list) {
            $list_arr = explode(',', $list);
            if(!empty($list_arr[$key])) {
                unset($list_arr[$key]);
            }
            $calculated_interests[$lang] = implode(',', $list_arr);
        }

        $user->calculated_interests = serialize($calculated_interests);
        $user->save(false);

        UsersWithSharedInterests::setNeedUpdateSharedInterests($user->id);

        return ['status' => 'success'];
    }

    public function actionGetCalculatedInterestByListNumber() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user && !empty($data['entered_text']) && !empty($data['user_lang'])) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $calculated_interests = unserialize($user->calculated_interests);
        $key = ((int) $data['entered_text'] - 1);
        $choosed_interests = explode(',', $calculated_interests[$data['user_lang']])[$key];

        if(empty($choosed_interests)) $choosed_interests = '';

        return ['status' => 'success', 'choosed_interests' => $choosed_interests];
    }

    public function actionClearAllInterests() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $user->calculated_interests = '';
        $user->save(false);

        return ['status' => 'success'];
    }
    public function actionGetUserConnections() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'connections' => UserConnections::getUserConnections($user->id)];
    }
    public function actionGetUserMatches() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'matches' => Matches::getUserMatches($user->id)];
    }
    public function actionGetUserSentInvites() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'connections' => UserConnections::getUserSentInvites($user->id)];
    }
    public function actionGetUserSentPendingInvites() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'connections' => UserConnections::getUserSentPendingInvites($user->id)];
    }
    public function actionIncrementUserSentPendingInvitation() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        return UserConnections::IncrementUserSentPendingInvitation($user->id, $data['user_id_2']);
    }
    public function actionGetUserSentPendingInvitation() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        return UserConnections::GetUserSentPendingInvitation($user->id, $data['user_id_2']);
    }
    public function actionGetUserRejectedInvites() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'connections' => UserConnections::getUserRejectedInvites($user->id)];
    }
    public function actionGetUserPendingInvites() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        return ['status' => 'success', 'connections' => UserConnections::getUserPendingInvites($user->id)];
    }

    public function actionGetUsersByAnyAlias() {
        //find users which are not connected with current user
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        $users = User::find()->where(['publicAlias' => $data['alias']])->orWhere(['telegram_alias' => $data['alias']])->andWhere(['<>','id', $user->id])->asArray()->all();
        return $users;
    }
    public function actionSetUserConnection(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        if (!UserConnections::setUserConnection($user->id,$data['user_id_2'])) return ['status' => 'error'];
        $user_to = User::find()->where(['id' => $data['user_id_2']])->one();
        Notifications::createNotification(Notifications::CONNECTION_REQUEST, $user, $user_to);
        return ['status' => 'success'];

    }
    public function actionDeleteUserConnection(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        return UserConnections::DeleteUserConnection($data['connection_id']);
    }
    public function actionGetUserByUserId(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = User::find()->where(['id' => $data['user_id']])->asArray()->one();
        if ($user === NULL) return ['status' => 'error'];
        return ['status' => 'success', 'user' => $user];

    }
    public function actionAcceptUserConnectionRequest(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        Notifications::setAsRead($data['notification_id']);
        $user_1 = User::find()->where(['id' => $data['user_id_1']])->one();
        $user_2 = User::find()->where(['id' => $data['user_id_2']])->one();
        Notifications::createNotification(Notifications::CONNECTION_ACCEPTED, $user_2, $user_1);
        return UserConnections::AcceptUserConnectionRequest($data['user_id_1'],$data['user_id_2']);
    }
    public function actionDeclineUserConnectionRequest(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        Notifications::setAsRead($data['notification_id']);
        $user_1 = User::find()->where(['id' => $data['user_id_1']])->one();
        $user_2 = User::find()->where(['id' => $data['user_id_2']])->one();
        Notifications::createNotification(Notifications::CONNECTION_REJECTED, $user_2, $user_1);
        return UserConnections::DeclineUserConnectionRequest($data['user_id_1'],$data['user_id_2']);
    }
    public function actionCheckUserConnection(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        return ['status' => 'success','connection' => UserConnections::CheckUserConnection($data['user_id_1'],$data['user_id_2'])];
    }

    /*expressions*/
    public function actionStartCreatingExpressions() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }
        $new_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();
        if($new_expression === NULL){
            $new_expression = new CreativeExpressions();
            $new_expression->user_id = $user->id;
            $new_expression->status = 'process_of_creation';
            $new_expression->save(false);
        }
        return ['status' => 'success'];
    }

/*    public function actionGetCreativeTypes() {//DEPRECATED
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $creative_types = array_column(CreativeTypes::find()->all(), 'type_' . $user->language, 'id');
        return ['status' => 'success', 'creative_types' => $creative_types];
    }*/

    public function actionSetCreativeTypeToExpression() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['type'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

/*        $find_creative_type = CreativeTypes::find()
            ->where(['type_en' => $data['type_title']])
            ->orWhere(['type_ru' => $data['type_title']])
            ->orWhere(['type_et' => $data['type_title']])
            ->asArray()
            ->one();

        if(empty($find_creative_type)) {
            return ['status' => 'error', 'text' => 'No such type was found! Try using a type from the suggested variants.'];
        }*/

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if(!empty($cur_expression)) {
            $cur_expression->type_enum = $data['type'];
            $cur_expression->save(false);
        }

        return ['status' => 'success'];
    }

    public function actionSetDescriptionToExpression()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['desc'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (!empty($cur_expression)) {
            $cur_expression->description = $data['desc'];
            $cur_expression->save(false);
        }

        return ['status' => 'success'];
    }

    public function actionSetTagsToExpression()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['tags'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (!empty($cur_expression)) {
            $cur_expression->tags = $data['tags'];
            $cur_expression->save(false);
        }

        return ['status' => 'success'];
    }
    public function actionSetExpirationToExpression()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['expiration'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (!empty($cur_expression)) {
            $cur_expression->active_period = time() + intval($data['expiration'])*60*60;
            $cur_expression->save(false);
        }

        return ['status' => 'success'];
    }

    public function actionSetUrlContentToExpression() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['url'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (!empty($cur_expression)) {
            $cur_expression->content = $data['url'];
            $cur_expression->save(false);
        }

        return ['status' => 'success'];
    }
    public function actionSetTextContentToExpression() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['text'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if($cur_expression === NULL) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        if($cur_expression->type_enum !== 'Text') return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $cur_expression->content = $data['text'];
        $cur_expression->save(false);

        return ['status' => 'success'];
    }
    public function actionSetFileContentToExpression() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['file_id'])) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (!empty($cur_expression)) {
            $new_content = CreativeExpressions::uploadFileFromTelegram($user->id, $data['file_id'], $data['supported_formats']);
            if($new_content === 'unsupported_format') return ['status' => 'error', 'text' => 'unsupported_format'];
            $cur_expression->content = $new_content;
            $cur_expression->save(false);
        }

        return ['status' => 'success'];
    }

    public function actionCancelExpressionCreation() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (!empty($cur_expression) && !empty($cur_expression->content)) {
            CreativeExpressions::removeFileFromExpression($cur_expression->id);
        }

        CreativeExpressions::deleteAll(['user_id' => $user->id, 'status' => 'process_of_creation']);

        return ['status' => 'success'];
    }

    public function actionExpressionFinishedCreation() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $cur_expression = CreativeExpressions::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => 'process_of_creation'])
            ->one();

        if (empty($cur_expression)) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        //$cur_expression->active_period = time() + 3600 * 24;
        $cur_expression->upload_date = time();
        $cur_expression->status = 'active';
        $cur_expression->save(false);

        return ['status' => 'success'];
    }
    public function actionGetUserCreativeExpressions(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if ($user === false) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        $res = CreativeExpressions::getCreativeExpressionsByUser($user->id);

        if($res === NULL)
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        else{
            foreach ($res as $id=>$rs){
                if(!empty($rs['content']) AND strlen($rs['content'])>500) {
                    $res[$id]['content'] = substr($rs['content'],0,500).'...';
                }
            }
            return ['status' => 'success', 'data' => $res];
        }

    }
    public function actionSetUserRegistrationLovecoins(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if (empty($data)) return ['status' => 'error'];
        $user = TelegramApi::validateAction($data);
        if (!$user) return ['status' => 'error', 'text' => 'Error! Try again later.'];
        //generate Lovecoin
        //return PartnerRuleAction::createAction(1,$user['id']);
        return PartnerRuleAction::actionRegistrationLovestar($user->id);
    }
    public function actionGetSwipesQueue(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $res = TelegramApi::validateWebAppRequest($data['initData']);
        if($res['status'] == false){
            if(isset($res['message']))return ['error' => 'Error! '.$res['message']];
            return ['error' => 'Error! Try again later.'];
        }

        $user = $res['user'];
        //new users 2nd round
        $users_with_shared_interests = UsersWithSharedInterests::getUserWithSharedInterests($user['id']);
        $creative_expressions = array();
        foreach ($users_with_shared_interests as $us) {
            //CreativeExpressions::setMockupData($us['id'],true);
            $expr_list = CreativeExpressions::getCreativeExpressionsByUser($us['user_id']);
            foreach ($expr_list as $expr){
                if(empty($expr['content']))continue;
                if(MatchAction::doesActionExist($user['id'], $expr['id']) == false){
                    if($expr['active_period'] == NULL)$expr['active_period'] = time()+24*60*60;
                    $creative_expressions[] = $expr;
                }
            }
        }
        //my connections, 1st round
        $user_friends = UserConnections::getUserConnections($user['id']);
        foreach ($user_friends as $us) {
            $expr_list = CreativeExpressions::getCreativeExpressionsByUser($us['user_id']);
            foreach ($expr_list as $expr){
                if(empty($expr['content']))continue;
                if(MatchAction::doesActionExist($user['id'], $expr['id']) == false){
                    if($expr['active_period'] == NULL)$expr['active_period'] = time()+24*60*60;
                    $creative_expressions[] = $expr;
                }
            }
        }
        usort($creative_expressions, function($a, $b){
            if(intval($a['active_period'])>intval($b['active_period']))
                return 1;
            elseif(intval($a['active_period'])>intval($b['active_period']))
                return -1;
            else
                return 0;
        });

        return $creative_expressions;
    }
    public function actionMatchAction()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $res = TelegramApi::validateWebAppRequest($data['initData']);
        if ($res['status'] == false) {
            return ['error' => 'Error! Try again later.'];
        }

        $user = $res['user'];
        $match = false;
        $CE = NULL;
        $new_friend_name = NULL;
        if(intval($data['action_result']) == 1){//like
            $rs = MatchAction::didUserLikedAnyOfOursExpression($user['id'], intval($data['expression_user_id']));
            if($rs !== NULL){//return MatchAction on success or NULL
                //match
                $match = true;
                $CE = CreativeExpressions::find()->where(['id' => $rs->expression_id])->one();
                $new_friend = User::find()->where(['id' => intval($data['expression_user_id'])])->one();
                if(empty($new_friend->telegram_alias))
                    $new_friend_name = $new_friend->publicAlias;
                else
                    $new_friend_name = $new_friend->publicAlias.'(@'.$new_friend->telegram_alias.')';
                Matches::addMatch(intval($data['expression_user_id']), $user['id']);
                Notifications::createNotification(Notifications::NEW_MATCH, $user, $new_friend);

            }
        }

        $res = MatchAction::addAction($user['id'], intval($data['expression_id']), intval($data['expression_user_id']), intval($data['action_result']));
        if ($res['status'] == false) return ['error' => 'Error! Try again later.'];
        return ['match' =>  $match,'CE' => $CE, 'new_friend_name' => $new_friend_name];

    }
    static function actionOpenMatches () {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $res = TelegramApi::validateWebAppRequest($data['initData']);
        if ($res['status'] == false) {
            return false;//['error' => 'Error! Try again later.'];
        }

        $user = $res['user'];
        $matches = Matches::getUserMatches($user->id);
        $i=1;
        $text = 'My matches'."\n";
        foreach ($matches as $item) {
            $user_name_text = $item['user']['publicAlias'];
            if (!empty($item['user']['telegram_alias'])) $user_name_text = '@' . $item['user']['telegram_alias'] . ' (' . $user_name_text . ')';
            $text .= $i . '. ' . $user_name_text . ' ' . 'created on' . ' ' . date('j/m/y', strtotime($item['timestamp'])) . "\n";
            $i++;
        }
        $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage";//?chat_id={$user->telegram}&text={$text}";
        CurlHelper::curl($url,array('chat_id' => $user->telegram, 'text' => $text));
        return true;
    }
    public function actionWebAppValidate(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $initDataArray = explode('&', rawurldecode($data['initData']));
        $needle        = 'hash=';
        $hash          = '';
        //parse_str($data['initData'], $output);
        //$ob = json_decode($output['user'],true);
        //return ['user8'=>$ob['id']];
        foreach ($initDataArray as &$dataq) {
            if (substr($dataq, 0, \strlen($needle)) === $needle) {
                $hash = substr_replace($dataq, '', 0, \strlen($needle));
                $dataq = null;
            }
        }
        $initDataArray = array_filter($initDataArray);
        sort($initDataArray);
        $data_check_string = implode("\n", $initDataArray);
        $secret_key = hash_hmac('sha256', TelegramBotId,'WebAppData', true);
        $local_hash = bin2hex(hash_hmac('sha256', $data_check_string, $secret_key, true));
        if($local_hash === $hash)
            return true;
        else
            return false;//[$local_hash, $hash, $data['initData']];
    }

    public function actionClaimMyLovestars() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);

        if (!$user || empty($data['code'])) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        $code = InvitationCodes::find()
            ->where(['code' => $data['code']])
            ->andWhere(['not', ['ruleActionId' => null]])
//            ->andWhere(['registered_user_id' => null])
            ->one();

        if(empty($code)) {
            InvitationCodesLogs::addToLog($user->id, $data['code'], 'Action: Claim My Lovestars. Error: There is no such code.');
            return ['status' => 'error', 'text' => 'This code is not valid or has already been redeemed'];
        }

        if(!empty($code->registered_user_id)) {
            InvitationCodesLogs::addToLog($user->id, $data['code'], 'Action: Claim My Lovestars. Error:This code has already been redeemed');
            return ['status' => 'error', 'text' => 'This code is not valid or has already been redeemed'];
        }

        $lovestars = Lovestar::find()->where(['issuingAction' => $code->ruleActionId])->all();

        if(empty($lovestars)) {
            return ['status' => 'error', 'text' => 'This code is not valid or has already been redeemed'];
        }

        $code->registered_user_id = $user->id;
        $code->save(false);

        foreach ($lovestars as $lovestar) {
            $lovestar->currentOwner = $user->id;
            $lovestar->save(false);
        }

        User::addedLovestarsCount($user->id, count($lovestars));

        return ['status' => 'success', 'emitted_lovestars' => count($lovestars)];
    }
    public function actionNotificationsRead($ntId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        $res = Notifications::setAsRead(intval($ntId));
        if($res)
            return ['status' => 'success'];
        else
            return ['status' => 'error'];
    }
    public function actionNotifications()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        if(isset($data['messageTypes'])) $messageTypes = explode(',', strtoupper($data['messageTypes']));
        $nots = Notifications::find()->where(['user_id' => $user->id])->all();
        $return_array=[];
        foreach ($nots as $nt){
            if(isset($data['messageTypes'])){
                if(!in_array($nt->type, $messageTypes)) continue;
            }
            if(isset($data['hoursAgo'])){
                if((time() - strtotime($nt->created_at))/3600 > intval($data['hoursAgo'])) continue;
            }
            if(isset($data['readStatus'])){
                if($data['readStatus'] === 'read'){
                    if($nt->read_status !== true) continue;
                }
                if($data['readStatus'] === 'unread'){
                    if($nt->read_status !== false) continue;
                }
            }
            $return_array[] = ['id' => $nt->id, 'type' => $nt->type, 'messageCode' => $nt->message_code, 'messageParameters' => $nt->params, 'createdAt' => $nt->created_at, 'readStatus' => $nt->read_status];
        }
        return ['status' => 'success', 'data' => $return_array];
    }
    public function actionNotificationsDelete($ntId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        $res = Notifications::deleteOne(intval($ntId));
        if($res)
            return ['status' => 'success'];
        else
            return ['status' => 'error'];

    }
    public function actionNotificationsUnreadCount()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];

        return ['status' => 'success', 'data' => Notifications::unreadCount($user->id)];
    }
    public function actionNotificationsReadAll()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        $res = Notifications::setAllAsRead($user->id);
        if($res)
            return ['status' => 'success'];
        else
            return ['status' => 'error'];
    }
    public function actionNotificationsDetails($ntId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        $res = Notifications::find()->where(['id' => intval($ntId)])->one();
        if($res !==NULL)
            return ['status' => 'success','data' => $res];
        else
            return ['status' => 'error'];
    }
    public function actionGetInterestsAnswers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        $res = UserInterestsAnswers::find()->where(['user_id' => $user->id])->asArray()->all();
        if($res === NULL)
            return ['status' => 'success','data' => []];
        else
            return ['status' => 'success','data' => $res];
    }
    public function actionMessageCounterIncrement()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        User::MessageCounterIncrement($user->id);
        return ['status' => 'success'];

    }
    public function actionSetMessageCounter()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        if(User::setMessageCounter($user->id, $data['message_counter']))
            return ['status' => 'success'];
        else
            return ['status' => 'error'];
    }
    public function actionSetInterestsAnswers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        $user = TelegramApi::validateAction($data);
        if($user === false) return ['status' => 'error', 'text' => 'Unknown user'];
        if(UserInterestsAnswers::setUserInterestsAnswers($user->id, $data['question_type'], $data['answer']))
            return ['status' => 'success'];
        else
            return ['status' => 'error'];
    }


}
