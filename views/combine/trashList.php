<?php
	use yii\helpers\Url;
	$this->registerJsFile("/js/combine.js", ['depends' => ['yii\bootstrap\BootstrapPluginAsset','app\assets\AppAsset']]);
	$this->registerCssFile("/css/combine.css");
?>

	<script>var pageName = 'trash';</script>
	<div id="labels" class="col-md-8 left">
		<p><a href="<?= Url::to(['combine/index']) ?>" class="btn btn-sm btn-warning">Return</a></p>

	<?php foreach ($groupName as $groupId => $name) :?>
		<div id="group<?=$groupId ?>" draggable="true" class="btn btn-sm <?= (isset($isGroup[$groupId])) ? "btn-success" : "btn-primary" ?> mybutton"><?=$name ?></div>
	<?php endforeach;?>

	</div>