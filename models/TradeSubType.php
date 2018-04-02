<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trade_sub_type".
 *
 * @property string $id
 * @property string $name
 * @property string $pid
 */
class TradeSubType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'common_trade_sub_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'pid'], 'required'],
            [['trade_type_id'], 'integer'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '分类名',
            'pid' => '父分类',
        ];
    }

    // /**
    //  * category and article has the relation of many2many
    //  *
    //  * Since that the category with pid ==1000 is the parent category
    //  * So where we meet the category that pid ==1000, we should get the sub categories
    //  * @return [type] [description]
    //  */
    // public function getArticles()
    // {
    //   //TODO:
    //     // //Parent category
    //     // if($this->pid == 1000){
    //     //     //get all categories
    //     //     $category_ids = [$this->pid];
    //     //     foreach (SELF::find()->where(['pid' => $this->id])->each() as $category) {
    //     //           $category_ids[] = $category->id;
    //     //     }

    //     //     //Select * from article left join article_category as a on a.article_id = article.id
    //     //     //where a.category_id in ...
    //     //     return (new \yii\db\Query())
    //     //         ->select('article.*')
    //     //         ->from('article')
    //     //         ->join('LEFT JOIN', 'article_category', 'article_category.article_id = article.id')
    //     //         ->where(['in','article_category.category_id',$category_ids])
    //     //         ->all();
    //     // }
    //     // //Sub category
    //     // //the same as before
    //     // return (new \yii\db\Query())
    //     //         ->select('article.*')
    //     //         ->from('article')
    //     //         ->join('LEFT JOIN', 'article_category', 'article_category.article_id = article.id')
    //     //         ->where(['article_category.category_id'=>$this->id])
    //     //         ->all();

    // }

}
