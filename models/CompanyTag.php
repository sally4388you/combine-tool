<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "company_tag".
 *
 * @property string $id
 * @property string $tag
 * @property string $company_id
 */
class CompanyTag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'common_company_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['company_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'company_id' => 'Company ID',
            'isdelete' => 'Isdelete',
        ];
    }

    public function getCompany_trade(){
        return $this->hasOne(CompanyTrade::className() , ['group_id' => 'group_id']);
    }

    public function getCompany_tag(){
        return $this->hasMany(CompanyTag::className() , ['group_id' => 'group_id']);
    }
}
