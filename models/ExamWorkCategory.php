<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bszt_exam_work_category".
 *
 * @property string $id
 * @property string $exam_id
 * @property string $work_category_id
 */
class ExamWorkCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bszt_exam_work_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exam_id', 'work_category_id'], 'required'],
            [['exam_id', 'work_category_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exam_id' => 'Exam ID',
            'work_category_id' => 'Work Category ID',
        ];
    }

    /**
     * batch insert array data
     * @param  [type] $exam_id      [description]
     * @param  [type] $record_array [description]
     * @return [type]               [description]
     */
    public function batch_insert($exam_id , $array){
        $tpl ='(@exam_id , @work_category_id),';
        $sql = 'insert into bszt_exam_work_category (exam_id , work_category_id) values ';
        foreach ($array as $value) 
            $sql .=str_replace( '@work_category_id',$value,
                str_replace('@exam_id', $exam_id, $tpl));
        $sql = substr($sql, 0, -1).';';
        try{
           Yii::$app->getDb()->createCommand($sql)->execute();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}
