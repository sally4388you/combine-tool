<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Article */
$groupby = Yii::$app->request->get('groupby','');
if( empty($groupby) )
	$this->title = '更新文章: ' . ' ' . $model->title;
else
	$this->title = '进行分类: ' . ' ' . $model->title;
?>
<div class="article-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <h5><strong>您正在修改的是第 <?php echo $model->id?> 条数据</strong></h5>

<?php if(empty($groupby)):?>
    <?=
    $this->render('_formUpdate', [
        'model' => $model,
    ])
    ?>
<?php else:?>
	<?=
    $this->render('_formGroupby', [
        'model' => $model,
    ])
    ?>
<?php endif;?>

</div>
