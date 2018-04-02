<?php

namespace app\models;

use Yii;
use app\models\ExamWorkCategory;
use app\models\QuestionAnswer;
use app\models\CompanyTag;

/**
 * This is the model class for table "bszt_exam".
 *
 * @property string $id
 * @property string $company_tag_id
 * @property string $work_name
 * @property string $year
 */
class Exam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bszt_exam';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_tag_id', 'work_name', 'year'], 'required'],
            [['company_tag_id'], 'integer'],
            [['year'], 'safe'],
            [['work_name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_tag_id' => 'Company Tag ID',
            'work_name' => 'Work Name',
            'year' => 'Year',
        ];
    }


    public function getExam_work_categories(){

        return $this->hasMany(ExamWorkCategory::className(), ['exam_id' => 'id']);
    }

    public function getQuestion_answers(){

        return $this->hasMany(QuestionAnswer::className(), ['exam_id' => 'id']);
    }

    public function getCompany_tag(){
        return $this->hasOne(CompanyTag::className(),['id'=>'company_tag_id']);
    }

}
