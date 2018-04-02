<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use app\models\ArticleTradeSubType;
use app\models\ArticleWorkCategory;
use app\models\ArticleCompanyTag;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $info
 * @property string $time
 * @property string $fromlink
 * @property string $author
 */
class Article extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'mjfx_article';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'info', 'fromlink','year'], 'required'],
            [['info'], 'string'],
            [['time', 'author'], 'safe'],
            [['title'], 'string', 'max' => 400],
            [['fromlink'], 'url'],
            [['fromlink'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => '标题',
            'info' => '正文',
            'time' => '录入时间',
            'year' => '年份',
            'fromlink' => '来源链接',
            'author' => '录入'
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [

            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'info',
                ],
                'value' => function($event) {
                $this->time = date('Y-m-d H:i:s');
                $this->author = Yii::$app->user->getIdentity()->username;
                return $this->info;
            },
            ],
        ];
    }

    /**
     * before save , deal with the title 
     * @return [type] [description]
     */
    public function beforeSave($insert)  
    {  
        if(parent::beforeSave($insert)){  
            
            $title = $this->title;

            //for utf8
            $title = preg_replace('/(\\\\)|(\*)|(\$)|(#)|(。)|(，)/u',' ',$title);
            $title = preg_replace('/(&mdash;)|(―)|(—)|(－)|(–)/u','-',$title);
            $title = preg_replace('/(&#40;)|(（)|(【)|(〖)|(『)|(\[)/u','(',$title);
            $title = preg_replace('/(&#41;)|(）)|(】)|(〗)|(』)|(\])/u',')',$title);
            $title = preg_replace('/(&ldquo;)|(&rdquo;)|(“)|(”)|(‘)|(’)|(\')|(")/u','"',$title);
            $title = preg_replace('/(•)|(・)/u','·',$title);
            $title = preg_replace('/(&nbsp;)|(　)|( )|( )/u',' ',$title);

            $this->title = $title;
            return true;  
        }else{  
            return false;  
        }  
    }  


    public function softDelete(){
        $this->deleted_at = date('Y-m-d H:i:s');
        $this->groupby = null;
        $this->save();
    }

    /**
     * get All categories by article id
     *
     * Select category.* from subcategory left join article_subcategory 
     * on article_category.subcategory_id = subcategory.id
     * where article_subcategory.article_id = $this->id
     *
     *
     * just use the relation defined in yii
     * @return array
     */
    public function getTrade_sub_types(){
        // if($this->id)
        // return (new \yii\db\Query())
        //         ->select('subcategory.*')
        //         ->from('subcategory')
        //         ->join('LEFT JOIN', 'article_subcategory', 'article_subcategory.subcategory_id = subcategory.id')
        //         ->where(['article_subcategory.article_id'=>$this->id])
        //         ->distinct()
        //         ->all();
        return $this->hasMany(ArticleTradeSubType::className(), ['article_id' => 'id']);
    }

    /**
     * get All jobs by article id     
     * 
     * Select job.* from job left join article_job 
     * on article_job.job_id = job.id
     * where article_job.article_id = $this->id
     *
     * 
     * just use the relation defined in yii
     * @return [type] [description]
     */
    public function getWork_categories(){
        // if($this->id)
        // return (new \yii\db\Query())
        //         ->select('job.*')
        //         ->from('job')
        //         ->join('LEFT JOIN', 'article_job', 'article_job.job_id = job.id')
        //         ->where(['article_job.article_id'=>$this->id])
        //         ->distinct()
        //         ->all();
        return $this->hasMany(ArticleWorkCategory::className(), ['article_id' => 'id']);
    }

    /**
     * get All comptags by article id
     *
     * select comptag.name from comptag left join article_comptag 
     * on article_comptag.comptag_id = comptag.id
     * where article_comptag.article_id = $this->id 
     *
     * 
     * just use the relation defined in yii
     * @return [type] [description]
     */
    public function getCompany_tags(){
        // if($this->id)
        // return (new \yii\db\Query())
        //         ->select('comptag.name')
        //         ->from('comptag')
        //         ->join('LEFT JOIN', 'article_comptag', 'article_comptag.comptag_id = comptag.id')
        //         ->where(['article_comptag.article_id'=>$this->id])
        //         ->distinct()
        //         ->all();
        return $this->hasMany(ArticleCompanyTag::className(), ['article_id' => 'id']);
    }

}
