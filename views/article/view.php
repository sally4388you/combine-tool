<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\TradeSubType;
use app\models\WorkCategory;
use app\models\CompanyTag;

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = $model->title;

    //Cache read first
    //
    //2 days = 2*24*60*60
    $expire = 172800;

    //trade sub type cache
    if(\Yii::$app->cache->exists('view_trade_sub_type_array')){
        $trade_sub_type_cache = \Yii::$app->cache->get('view_trade_sub_type_array');
        $trade_sub_type_array = $trade_sub_type_cache['trade_sub_type_array'];
    }else{
        $trade_sub_types = TradeSubType::find()->all();    
        $trade_sub_type_array = [];
        $trade_sub_type_trade_type_array =[];
        foreach ($trade_sub_types as $trade_sub_type) {
            $trade_sub_type_array[$trade_sub_type->id] =$trade_sub_type->name;
            $trade_sub_type_trade_type_array[$trade_sub_type->trade_type_id][] = ['id' => $trade_sub_type->id , 'name'=>$trade_sub_type->name];
        }
        \Yii::$app->cache->set('view_trade_sub_type_array',['trade_sub_type_array'=>$trade_sub_type_array,'trade_sub_type_trade_type_array'=>$trade_sub_type_trade_type_array],$expire);
    }

    //work category cahe
    if(\Yii::$app->cache->exists('view_work_category_array')){
        $work_category_array = \Yii::$app->cache->get('view_work_category_array');
    }else{
        $work_categories = WorkCategory::find()->orderBy('sort')->all();
        $work_category_array = [];
        foreach ($work_categories as $work_category) 
            $work_category_array[$work_category->id] = $work_category->name;
        \Yii::$app->cache->set('view_work_category_array',$work_category_array,$expire);
    }

    //company tag cahe
    if(\Yii::$app->cache->exists('view_company_tag_array')){
        $company_tag_array = \Yii::$app->cache->get('view_company_tag_array');
    }else{
        $company_tags = CompanyTag::find()->all();
        $company_tag_array = [];
        foreach ($company_tags as $company_tag) 
            $company_tag_array[$company_tag->id] = $company_tag->name;
        \Yii::$app->cache->set('view_company_tag_array',$company_tag_array,$expire);
    }
?>
<div class="article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('进行分类', ['update', 'id' => $model->id,'groupby'=>1], ['class' => 'btn btn-info']) ?>
        
        <?=
        Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除这篇文章?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

<?php
    $model_work_categories = $model->work_categories;
    $work_category_html = '';
    foreach ($model_work_categories as $model_work_category) {
        $work_category_html .= '<span class="label label-info">'.$work_category_array[$model_work_category->work_category_id].'</span>';
    }

    $model_trade_sub_types = $model->trade_sub_types;

    if($model_trade_sub_types &&$model_trade_sub_types[0]->trade_sub_type_id == 0)
        $trade_sub_type_html ='<span class="label label-info">通用资料</span>';
    else{
        $trade_sub_type_html = '';
        foreach ($model_trade_sub_types as $model_trade_sub_type) {
            $trade_sub_type_html .= '<span class="label label-info">'.$trade_sub_type_array[$model_trade_sub_type->trade_sub_type_id].'</span>';
        }
    }

    $model_company_tags = $model->company_tags;
    $company_tag_html = '';
    foreach ($model_company_tags as $model_company_tag) {
        $company_tag_html .= '<span class="label label-info">'.$company_tag_array[$model_company_tag->company_tag_id].'</span>';
    }

?>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'author',
            'time',
            'fromlink',
            'year',   
            [ 
                'label' => '行业',
                'value' => $trade_sub_type_html,
                'format' => 'html'
            ],      
            [ 
                'label' => '职位',
                'value' => $work_category_html,
                'format' => 'html'
            ],
            [ 
                'label' => '公司标签',
                'value' => $company_tag_html,
                'format' => 'html'
            ],
            'info:raw',
        ],
    ])
    ?>

</div>
