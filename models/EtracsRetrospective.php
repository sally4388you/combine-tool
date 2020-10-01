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
class EtracsRetrospective extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'etracs_retrospective';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_1', 'question_2', 'question_3'], 'required'],
            [['question_1', 'question_2', 'question_3'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_1' => 'Mooncake 1',
            'question_2' => 'Mooncake 2',
            'question_3' => 'Mooncake 3',
            'created_at' => 'Create Time',
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            
            $this->created_at = date('y-m-d H:i:s');
               
            return true;
        }
        else {
            return false;
        }
    }
}
