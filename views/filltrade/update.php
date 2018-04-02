<?php

use yii\helpers\Html;
// use app\assets\LayerAsset;

// LayerAsset::register($this);
$this->registerCssFile("/css/filltrade.css");

/* @var $this yii\web\View */
/* @var $model app\models\CompanyTag */

$this->title = '添加行业: ' . ' ' . $model->name;
?>
<div class="company-tag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'id' => $id,
        'model' => $model,
        'detail' => $detail,
        'combines' => $combines,
        'tradeInfo' => $tradeInfo,
        'trade_type_array' => $trade_type_array,
        'trade_sub_type_array' => $trade_sub_type_array,
        'trade_sub_type_trade_type_array' => $trade_sub_type_trade_type_array,
    ]) ?>

</div>
