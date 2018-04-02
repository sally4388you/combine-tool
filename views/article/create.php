<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = '添加文章';
?>
<div class="article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_formCreate', [
        'model' => $model,
    ])
    ?>

</div>
