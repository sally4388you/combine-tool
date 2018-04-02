<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_company_tag".
 *
 * @property string $id
 * @property string $article_id
 * @property string $company_tag_id
 */
class ArticleCompanyTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mjfx_article_company_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'company_tag_id'], 'required'],
            [['article_id', 'company_tag_id'], 'integer']
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
            'company_tag_id' => 'Comptag ID',
        ];
    }

    /**
     * this method is 4 batch insert
     * @param  [type] $article_id [description]
     * @param  [type] $array      [description]
     * @return [type]             [description]
     */
    public function batch_insert($article_id ,$array){
        $tpl ='(@article_id , @company_tag_id),';
        $sql = 'insert into mjfx_article_company_tag (article_id , company_tag_id) values ';
        foreach ($array as $value) 
            $sql .=str_replace( '@company_tag_id',$value,
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
