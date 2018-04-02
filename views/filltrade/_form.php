<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\CropperAsset;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyTag */
/* @var $form yii\widgets\ActiveForm */
CropperAsset::register($this);
$this->registerCssFile("/css/cropper.css");
$this->registerJsFile("/js/filltrade.js", ['depends' => '\yii\web\JQueryAsset']);
if (file_exists(dirname(dirname(dirname(__FILE__)))."/web/images/logo/".$id.".png")) {
    $src = "/images/logo/".$id.".png";
} else {
    $src = "/images/nopic.png";
}

?>

<div class="company-tag-form" id="crop-avatar">

    <!-- left part -->
    <div class="well col-md-6">
        <?php if (!isset($tradeInfo[1][0])) echo "fail in spider";?>
        <!-- 1.公司在大街上的首页网址
             2.公司行业
             3.公司名称
             4.logo网址 -->
        <?php for ($i = 0; $i < count($tradeInfo[1]); $i ++) :?>
        <div class="company-trade-detail">
            <img src="<?= $tradeInfo[4][$i] ?>" data-toggle="modal" rel="noreferrer" class="avatar-view">
            <span>
                <a href="<?= $tradeInfo[1][$i] ?>" target="_blank"><?= $tradeInfo[3][$i] ?></a><br />
                行业：<?= $tradeInfo[2][$i] ?>
            </span>
            <div></div>
        </div>
        <?php endfor;?>
    </div>

    <!-- right part -->
    <div class="col-md-6">

    <form id="labels">
        <img src="<?= $src ?>?t=3" id="logo">
        <label class="control-label">关联标签</label>
        <?php foreach ($combines as $combine) :?>
            <?php if ($combine->id != $model->id) :?>
                <h4 id='label<?= $combine->id ?>'><span class='label label-info'><?= $combine->name ?>&nbsp;<i class='fa fa-times'></i></span></h4>
            <?php endif;?>
        <?php endforeach;?>
    </form>

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'form-company-trade'],
        'action' => '/filltrade/submit?id='.$id,
        'id' => 'tags',
    ]); ?>
    <div class="form-group">
        <label class="control-label">行业</label>
        <input type="hidden" name="trade_sub_types" value="">
        <input type="hidden" name="next" value="0" id="isnext">
        <span id="span_categories" class="form-control dropdown-toggle" data-toggle="dropdown"></span>
        <div class="dropdown-menu dropdown-menu-wide dropdown-menu-multiselect animated fadeIn not-close" role="menu">
            <div class="selected-box category">
                <?php if($detail[0]->type): $type_ids = explode("+", $detail[0]->type)?>
                <?php if($type_ids[0] == 0):?>
                    <span class="btn category" data-id="0" data-name="通用资料">
                    通用资料
                         <a href="javascript:void(0);">
                            <i class="fa fa-times"></i>
                         </a>
                    </span>
                <?php else: foreach($type_ids as $typeId):?>
                    <span class="btn category" data-id="<?= $typeId ?>" data-name="<?= $trade_sub_type_array[$typeId] ?>">
                        <?= $trade_sub_type_array[$typeId] ?>
                         <a href="javascript:void(0);"><i class="fa fa-times"></i></a>
                    </span>
                <?php endforeach; endif;endif;?>
            </div>

            <div class="item-box">
                <div class="row">
                    <h5 class="col-xs-2 text-success text-right remove padding right">通用资料</h5>
                    <div class="col-xs-10 btn-fluid-list">
                        <span class="width-split-4 btn-fluid-list-block">
                            <a href="javascript:void(0);" class="btn category" data-id="0" data-name="通用资料">通用资料</a>
                        </span>
                    </div>
                </div>
                     <hr/>
                <?php foreach ($trade_type_array as $trade_sub_type_id => $trade_sub_type_name):?>
                <div class="row">
                    <h5 class="col-xs-2 text-success text-right remove padding right"><?php echo $trade_sub_type_name?></h5>
                    <div class="col-xs-10 btn-fluid-list">
                    <?php foreach ($trade_sub_type_trade_type_array[$trade_sub_type_id] as $trade_sub_type):?>
                        <span class="width-split-4 btn-fluid-list-block">
                            <a href="javascript:void(0);" class="btn category" data-id="<?php echo $trade_sub_type['id']?>" data-name="<?php echo $trade_sub_type['name']?>"><?php echo $trade_sub_type['name']?></a>
                        </span>
                    <?php endforeach;?>
                    </div>
                </div>
                     <hr/>
                <?php endforeach;?>
            </div>
        </div><br />
    
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        <?= Html::Button('返回', ['class' => 'btn btn-primary', 'id' => 'return']) ?>
        <?= Html::Button('保存并查看下一个', ['class' => 'btn btn-primary', 'id' => 'next']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    </div>

    <!-- bootstrap modal -->
    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            <form class="avatar-form" action="/filltrade/pictureshot" enctype="multipart/form-data" method="post">
                <input class="avatar-data" name="avatar_data" type="hidden">
                <input name="pic-id" type="hidden" value="<?= $id ?>" id="pic-id">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">图片截取</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="avatar-wrapper"></div>
                      </div>
                      <div class="col-md-3">
                        <div class="avatar-preview preview-md"></div>
                        <div class="avatar-preview preview-sm"></div>
                      </div>
                    </div>
                </div>
                <div class= "modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary avatar-save" type="submit">Save changes</button>
                </div>

            </form>
            </div>
        </div>
    </div>

</div>
