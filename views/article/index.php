<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '面经管理';
$groupby = Yii::$app->request->get('groupby','');
?>

<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加面经', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('待分类', ['index?groupby=1'], ['class' => 'btn btn-primary']) ?>
    </p>

   <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'class' => 'yii\grid\DataColumn', 
                'value' => function ($col) {
                    $title = $col->title;
                    $id = $col->id;
                    return "<a href='/article/view?id=$id' title='$title' target='_blank'>".mb_substr($title,0,20,'utf8')."...</a>";

                },
                'label' => '标题',
                'format' => 'raw'
            ],
            [
                'class' => 'yii\grid\DataColumn', 
                'value' => function ($col) {
                    $link = $col->fromlink;
                    return "<a href='$link' target='_blank'>".mb_substr($link,0,20,'utf8')."...</a>";
                },
                'label' => '来源链接',
                'format' => 'raw'
            ],
            'time',
            'author',   
            [
                'class' => 'yii\grid\DataColumn', 
                'value' => function ($col) {
                    $id = $col->id;
                    if( is_null($col->groupby) || empty($col->groupby) )
                        return "<a href='/article/update?id=$id&groupby=1' target='_blank'>前去分类</a>";
                    return $col->groupby." <a href='/article/update?id=$id&groupby=1' target='_blank' data-toggle='tooltip' data-placement='right' title='修改分类'><i class='fa fa-pencil'></i></a>";
                },
                'label' => '分类者',
                'format' => 'raw'
            ],
            'year',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons'=>
                [

                    'update'=>function ($url, $model, $key) use($groupby) {
                        $options = [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ];
                        
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $groupby == 1 ? $url.'&groupby=1':$url, $options);


                    }
                ]
            ],
        ],

    ]);
    ?>
</div>