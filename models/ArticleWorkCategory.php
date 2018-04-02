<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_work_category".
 *
 * @property string $id
 * @property string $article_id
 * @property string $job_id
 */
class ArticleWorkCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mjfx_article_work_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'work_category_id'], 'required'],
            [['article_id', 'work_category_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'work_category_id' => 'Work Category ID',
        ];
    }
    
    /**
     * this method is 4 batch insert
     * @param  [type] $article_id [description]
     * @param  [type] $array      [description]
     * @return [type]             [description]
     */
    public function batch_insert($article_id ,$array){
        $tpl ='(@article_id , @work_category_id),';
        $sql = 'insert into mjfx_article_work_category (article_id , work_category_id) values ';
        foreach ($array as $value) 
            $sql .=str_replace( '@work_category_id',$value,
                str_replace('@article_id', $article_id, $tpl));
        $sql = substr($sql, 0, -1).';';
        try{
           Yii::$app->getDb()->createCommand($sql)->execute();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}
