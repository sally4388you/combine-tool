<?php
	use yii\helpers\Url;
	$this->registerJsFile("/js/combine.js", ['depends' => ['yii\bootstrap\BootstrapPluginAsset','app\assets\AppAsset']]);
	$this->registerCssFile("/css/combine.css");
?>

	<div id="operation">
		<a href="/filltrade" class="list-btn">列表方式查看</a>
		<form id="renameForm" onsubmit="return false;">
			重命名：<input class="form-control" id="rename" readonly>
		</form>
		<div id="tips"></div>
	</div>
	<div id="labels" class="col-md-8">

	<?php foreach ($groupName as $groupId => $name): ?>
		<div id="group<?= $groupId ?>" draggable="true" 
			class="btn btn-sm <?=(isset($isGroup[$groupId])) ? "btn-success" : ((isset($isCombined[$groupId])) ? "btn-default" : "btn-primary") ?> mybutton"><?= $name ?></div>
	<?php endforeach; echo $count;?>

	</div>
	<div class="col-md-4">
		<div class="affixed-element-bottom js-affixed-element-bottom">
			<div class="panel panel-info my-panel" style="width: 350px;">
				<div class="panel-heading"><h3 class="panel-title">搜索</h3></div>
				<div class="panel-body my-panel-body" style="height: 200px;">
					<input type="text" class="form-control" placeholder="search" id="searchbox">
					<a href="javascript:void(0);" id="add"><i class="fa fa-plus fa-2x"></i></a>
					<div id="search"></div>
				</div>
			</div>
			<div class="panel panel-warning my-panel">
				<div class="panel-heading"><h3 class="panel-title">已合并</h3></div>
				<div class="panel-body my-panel-body" id="combined"></div>
			</div>
			<div class="panel panel-danger my-panel">
				<div class="panel-heading"><h3 class="panel-title">待处理</h3></div>
				<div class="panel-body my-panel-body" id="dealing"></div>
			</div>
			<a href="<?= Url::to(['combine/trash-list']) ?>" class="trash"><i class='fa fa-trash fa-2x'></i>删除名单</a>
			<a href="javascript:void(0);"><i class='fa fa-tags fa-2x'></i>处理</a>
			<a href="javascript:void(0);" class="js-popover" 
				data-container="body" data-toggle="popover" data-placement="right" data-html="true"
				data-content="颜色：白色：未处理；<br />
					蓝色：已处理，未合并；<br />
					绿色：标签中有被合并的标签；<br />
					红色：被点击；<br />
					橙色：被搜索到的标签。<br /><br />
					操作：拖拽：合并。<br />
					单击：搜索；查看已合并；从已合并中恢复到未合并状态<br />
					delete键：删除<br />
					s键，处理键：标记为已处理<br />
					双击：放入待处理区域">
				<i class='fa fa-warning fa-2x'></i>帮助
			</a>
			<a href="javascript:void(0)" id="list"><i class='fa fa-indent fa-2x'></i>列表</a>
		</div>
	</div>

	<!--bootstrap modal-->
	<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">关联面经和笔试题目</h4>
        </div>
        <div class="modal-body">
        	<div>面经
        		<div id="mj"></div>
        	</div>
        	<div>笔试题目
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
