<?php
	use yii\helpers\Url;
	$this->registerJsFile("/js/combine.js", ['depends' => ['yii\bootstrap\BootstrapPluginAsset','app\assets\AppAsset']]);
	$this->registerCssFile("/css/combine.css");
	$this->title = "Combine Tool";

	$session = Yii::$app->session;
	$lang = Yii::$app->request->get('lang', false);
	$lang = $lang ? $lang : ($session->has('language') ? $session->get('language') : Yii::$app->language);
?>

	<div id="operation" class="col-md-8">
		<form id="renameForm" onsubmit="return false;">
			<?= Yii::$app->params[$lang]['rename'] ?><input class="form-control" id="rename" readonly>
		</form>
        <div id="tips"></div>
        <a href="javascript:void(0);" class="js-popover legend" 
            data-container="body" data-toggle="popover" data-placement="right" data-html="true"
            data-content="<?= Yii::$app->params[$lang]['help_msg'] ?>">
            <i class="far fa-list-alt"></i> <?= Yii::$app->params[$lang]['help'] ?>
        </a>
        <a href="<?= Url::to(['combine/trash-list']) ?>" class="trash">
            <i class='fa fa-trash'></i> <?= Yii::$app->params[$lang]['deleted'] ?>
        </a>
	</div>
	<div id="labels" class="col-md-8">

	<?php foreach ($groupName as $groupId => $name): ?>
		<div id="group<?= $groupId ?>" draggable="true" 
			class="btn btn-sm <?=(isset($isGroup[$groupId])) ? "btn-success" : ((isset($isCombined[$groupId])) ? "btn-default" : "btn-primary") ?> mybutton"><?= $name ?></div>
	<?php endforeach; echo $count;?>

	</div>
	<div class="col-md-4" style="z-index: 20;">
		<div class="affixed-element-bottom js-affixed-element-bottom">
			<div class="panel panel-info my-panel" style="width: 350px;">
				<div class="panel-heading"><h3 class="panel-title"><?= Yii::$app->params[$lang]['search'] ?></h3></div>
				<div class="panel-body my-panel-body" style="height: 200px;">
					<input type="text" class="form-control" placeholder="search" id="searchbox">
					<a href="javascript:void(0);" id="add"><i class="fa fa-plus fa-2x"></i></a>
					<div id="search"></div>
				</div>
			</div>
			<div class="panel panel-warning my-panel">
				<div class="panel-heading"><h3 class="panel-title"><?= Yii::$app->params[$lang]['combined'] ?></h3></div>
				<div class="panel-body my-panel-body" id="combined"></div>
			</div>
			<div class="panel panel-danger my-panel">
				<div class="panel-heading"><h3 class="panel-title"><?= Yii::$app->params[$lang]['pending'] ?></h3></div>
				<div class="panel-body my-panel-body" id="dealing"></div>
			</div>

		</div>
	</div>

	<!--bootstrap modal-->
	<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"><?= Yii::$app->params[$lang]['related_title'] ?></h4>
        </div>
        <div class="modal-body">
        	<div><?= Yii::$app->params[$lang]['interview'] ?>
        		<div id="mj"></div>
        	</div>
        	<div><?= Yii::$app->params[$lang]['written_test'] ?>
        		<div id="exam"></div>
        	</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
    </div>
	<!--end bootstrap modal-->
