<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bszt_question_answer".
 *
 * @property string $id
 * @property string $question
 * @property string $answer
 * @property string $exam_id
 */
class QuestionAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bszt_question_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question', 'answer', 'exam_id'], 'required'],
            [['question', 'answer'], 'string'],
            [['exam_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Question',
            'answer' => 'Answer',
            'exam_id' => 'Exam ID',
        ];
    }

    /**
     * this method is 4 batch insert
     * @param  [type] $exam_id [description]
     * @param  [type] $array      [description]
     * @return [type]             [description]
     */
    public function batch_insert($exam_id ,$array){
        $tpl ='( \'@question\' , \'@answer\' , @exam_id ),';
        $sql = 'insert into bszt_question_answer ( `question` , `answer` , exam_id ) values ';
        foreach ($array as $value) 
            $sql .= str_replace( '@question' , mysql_real_escape_string( $value['question'] ),
                    str_replace( '@answer',mysql_real_escape_string( $value['answer'] ),
                    str_replace('@exam_id', $exam_id, $tpl)));
        $sql = substr($sql, 0, -1).';';
        try{
           Yii::$app->getDb()->createCommand($sql)->execute();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}
