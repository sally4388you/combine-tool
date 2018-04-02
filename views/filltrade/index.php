<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->registerCssFile("/css/filltrade.css");
$this->title = '公司行业';
$thisGroup = 0;
?>
<div class="company-tag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [//Group Id
                'value' => function($col) {
                    global $thisGroup;
                    $thisGroup = $col->group_id;
                    return $col->group_id;
                },
                'label' => 'id',
            ],
            [
                'value' => function($col) {
                    if (file_exists(dirname(dirname(dirname(__FILE__)))."/web/images/logo/".$col->group_id.".png")) {
                        $src = "/images/logo/".$col->group_id.".png";
                    } else {
                        $src = "/images/nopic.png";
                    }
                    return '<img src="'.$src.'" class="logo_m">';
                },
                'label' => 'logo',
                'format' => 'raw',
            ],
            [//company's name
                'value' => function($col) {
                    global $thisGroup;
                    $names = "";
                    foreach ($col->company_tag as $tag) {
                        if ($tag->id == $thisGroup) {
                            $thisName = "<a href='/filltrade/update?id=".$tag->id."'>".$tag->name."</a>";
                        }
                        else $thisName = $tag->name;
                        $names .= $thisName." / ";
                    }
                    $names = substr($names, 0, -2);
                    return $names;
                },
                'label' => '公司名称',
                'format' => 'raw',
            ],
            [//company's type
                'attribute' => 'company_trade.trade_name',
                'label' => '公司类别',
                'format' => 'raw',
            ],
            [//author
                'attribute' => 'company_trade.author',
                'label' => '修改人',
            ],
            [//time of revising
                'value' => 'company_trade.time',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'label' => '修改日期'
            ],
            [//actions
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>

</div>
