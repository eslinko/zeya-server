<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Partner".
 *
 * @property string $id
 * @property string $legalName
 * @property string $description
 * @property string $billingVATNumber
 * @property string $billingDetails
 * @property string $authHash
 */
class Partner extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Partner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['legalName', 'billingVATNumber', 'billingDetails'], 'required'],
          [['description'], 'string'],
          [['autoHash'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'legalName' => 'Legal Name',
            'description' => 'Description',
            'billingVATNumber' => 'Billing VAT Number',
            'billingDetails' => 'Billing Details',
        ];
    }
	
	static function filterPartner($params = []){
		$query = Partner::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				case 'legalName':
				case 'billingVATNumber':
					$query = $query->andWhere(['like', $param, '%' . $value . '%', false]);
					break;
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}

		return $query;
	}
    static public function createBotPartner(){
        $new_partner = Partner::find()->where(['id' => 1])->one();
        if($new_partner===NULL) {
            $new_partner = new Partner();
            $new_partner->id = 1;
        }

        $new_partner->legalName = 'BotPartner';
        $new_partner->description = 'BotPartner does bot actions';
        if($new_partner->save(false)){
            return $new_partner;
        }
        else{
            return NULL;
        }
    }

    static public function createVHPartner(){
        $new_partner = Partner::find()->where(['id' => 2])->one();

        if($new_partner===NULL) {
            $new_partner = new Partner();
            $new_partner->id = 2;
        }

        $new_partner->legalName = 'Viral Help';
        $new_partner->description = 'VH+Zeya4Eve.';
        $new_partner->authHash = password_hash(ViralHelpPartnerPassword, PASSWORD_DEFAULT);
        if($new_partner->save(false)){
            return $new_partner;
        }
        else {
            return NULL;
        }
    }

    static public function partnerPasswordVerify($partner_id, $password){
        $partner = Partner::findOne($partner_id);

        if(empty($partner)) {
            return false;
        }

        return password_verify($password, $partner->authHash);
    }
}
