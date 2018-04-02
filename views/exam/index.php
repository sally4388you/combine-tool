<!DOCTYPE html>
<html lang="en" ng-app="examinApp">
<head>
	<meta charset="UTF-8">
	<?= yii\helpers\Html::csrfMetaTags() ?>
	<title>笔经管理</title>
	<link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="/bower_components/fontawesome/css/font-awesome.css">
	<link rel="stylesheet" href="/bower_components/sentsinLayer/skin/layer.css">
	<link rel="stylesheet" type="text/less" href="/src/less/style.less">
	<script src="/bower_components/less/dist/less.js"></script>
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
	  <div class="container">
	    <div class="navbar-header">
	      <a class="navbar-brand" href="/">面经管理</a>
	      <a class="navbar-brand" href="/exams#/">笔试真题</a>
	    </div>

	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

	      <ul class="nav navbar-nav navbar-right">
	        <li><a href="/article/create">添加面经</a></li>
			<li><a href="/article?groupby=1">进行分类</a></li>
	        <li><a href="/exams#/new">添加真题</a></li>
	        <li>
        	<?php 
        	echo 
        	Yii::$app->user->isGuest ? '<a href="/site/login">登陆</a>':'<a href="/site/logout">注销 (' . Yii::$app->user->identity->username . ')</a>';
        	?>
            </li>
	      </ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav><!-- /nav -->
	
	<section ng-view></section>

	<script src="/bower_components/jquery/dist/jquery.js"></script>
	<script src="/bower_components/bootstrap/dist/js/bootstrap.js"></script>
	<script src="/bower_components/ueditor/ueditor.config.js"></script>
	<script src="/bower_components/ueditor/ueditor.all.js"></script>
	<script src="/bower_components/sentsinLayer/layer.js"></script>
	<script src="/bower_components/angularjs/angular.js"></script>
	<script src="/bower_components/angular-route/angular-route.js"></script>
	<script src="/bower_components/angular-resource/angular-resource.js"></script>
	<script src="/bower_components/angular-sanitize/angular-sanitize.js"></script>
	<script src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.js"></script>
	<script src="/src/js/app.js"></script>
</body>
</html>