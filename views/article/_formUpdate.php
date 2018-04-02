<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use gerpayt\yii2_ueditor\UEditorWidget;
/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'fromlink')->textInput(['maxlength' => true]) ?>

<div class="form-group">
    <label class="control-label">年份</label>
    <input name="Article[year]" type="hidden" value="<?php echo isset($model->year)? $model->year: '2015'?>">
    <div>
        <span class="btn year" data-year="2015">2015</span>
        <span class="btn year" data-year="2014">2014</span>
        <span class="btn year" data-year="2013">2013</span>
        <span class="btn year" data-year="2012">2012</span>
        <span class="btn year" data-year="2011">2011</span>
        <span class="btn year" data-year="2010">2010</span>
    </div>
</div>
    
    <?=
    $form->field($model, 'info')->widget(UEditorWidget::className(), ['height' => '400px', 'options' => [
            'config' => [
                "toolbars" => [
                    ['bold', 'underline', 'forecolor', 'fontsize'],
                ],
                "retainOnlyLabelPasted" => true,
                "pasteplain" => true,
                "enableContextMenu" => false,
                "serverUrl" => false,
            ]
    ]]);
    ?>
<input name="nextaction" value="" type="hidden">

    <div class="form-group">
        <?= Html::submitButton('保存并预览' ,['class' => 'btn btn-success','id'=>'preview']) ?>
        <?= Html::submitButton('保存并处理下一篇' ,['class' => 'btn btn-success','id'=>'next']) ?>
        <?= Html::submitButton('保存并返回分类' ,['class' => 'btn btn-primary','id'=>'groupby']) ?>
        <?= Html::Button('删除并跳转下一篇' ,['class' => 'btn btn-danger','id'=>'delete','data-id'=>$model->id]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
