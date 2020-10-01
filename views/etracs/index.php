<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Check */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'eTRACS Retrospective';
?>

<style>
    body {
        background-image: url("images/mid_autumn.jpeg"); 
        background-position-x: center; 
        background-repeat: no-repeat; 
        background-color: #182554;
        height: 998px;
    }
    form {
        width: 400px;
        margin: 300px auto;
    }
</style>


<?php $form = ActiveForm::begin(['method' => 'post']); ?>

<?= $form->field($model, 'question_1')->textInput()->error(false) ?>

<?= $form->field($model, 'question_2')->textInput()->error(false) ?>

<?= $form->field($model, 'question_3')->textInput()->error(false) ?>

<div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>