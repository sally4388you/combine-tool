<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_trade_sub_type".
 *
 * @property string $id
 * @property string $article_id
 * @property string $category_id
 */
class ArticleTradeSubType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mjfx_article_trade_sub_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'trade_sub_type_id'], 'required'],
            [['article_id', 'trade_sub_type_id'], 'integer']
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
            'trade_sub_type_id' => 'Trade Sub Type ID',
        ];
    }
    /**
     * this method is 4 batch insert
     * @param  [type] $article_id [description]
     * @param  [type] $array      [description]
     * @return [type]             [description]
     */
    public function batch_insert($article_id ,$array){
        $tpl ='(@article_id , @trade_sub_type_id),';
        $sql = 'insert into mjfx_article_trade_sub_type (article_id , trade_sub_type_id) values ';
        foreach ($array as $value) 
            $sql .=str_replace( '@trade_sub_type_id',$value,
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
