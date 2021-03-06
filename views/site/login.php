<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$session = Yii::$app->session;
$lang = Yii::$app->request->get('lang', false);
$lang = $lang ? $lang : ($session->has('language') ? $session->get('language') : Yii::$app->language);
Yii::$app->language = $lang;

$this->title = Yii::$app->params[$lang]['login'];
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::$app->params[$lang]['login_msg'] ?></p>

    <?php
    $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
    ]);
    ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

<?=
$form->field($model, 'rememberMe')->checkbox([
    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
])
?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton(Yii::$app->params[$lang]['login'], ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
</div>
