<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\CropperAsset;

CropperAsset::register($this);
$this->registerCssFile("/css/cropper.css");
$this->registerCssFile("/css/mjpic.css");
$this->registerJsFile("/js/mjpic.js", ['depends' => '\yii\web\JQueryAsset']);
?>
<div class="container" id="crop-avatar">

    <ul class="mj-pics">
      <li id="add-pic">
        <!-- Current avatar -->
        <div class="avatar-view" title="Add picture">
          <img src="../../images/add.png" alt="Avatar">
          <span id="count"><?= count($filenames) ?></span>
        </div>
      </li>

      <?php for ($i = count($filenames) - 1; $i >= 0; $i --) :?>

      <li id="li">
        <img src="../../images/mjpic_s/<?= $filenames[$i] ?>" alt="Avatar" title="<?= $filenames[$i] ?>">
        <div class="close-btn close" id="<?= preg_replace('/\.png/', '', $filenames[$i]) ?>"><i class="fa fa-times fa-2x"></i></div>
        <span><?= $filenames[$i] ?></span>
      </li>

      <?php endfor; ?>

    </ul>

    <!-- Cropping modal -->
    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form class="avatar-form" action="/mjpic/pictureshot" enctype="multipart/form-data" method="post">
            <div class="modal-header">
              <button class="close" data-dismiss="modal" type="button">&times;</button>
              <h4 class="modal-title" id="avatar-modal-label">上传图片</h4>
            </div>
            <div class="modal-body">
              <div class="avatar-body">

                <!-- Upload image and data -->
                <div class="avatar-upload">
                  <input class="avatar-data" name="avatar_data" type="hidden">
                  <label for="avatarInput">图片链接</label>
                  <input class="form-control avatar-input" id="avatarInput" type="text">
                  <input type="button" class="btn btn-primary" id="get-btn" value="获取">
                </div>

                <!-- Crop and preview -->
                <div class="row">
                  <div class="col-md-9">
                    <div class="avatar-wrapper"></div>
                  </div>
                  <div class="col-md-3">
                    <div class="avatar-preview preview-lg"></div>
                    <div class="avatar-preview preview-md"></div>
                    <div class="avatar-preview preview-sm"></div>
                  </div>
                </div>

                <div class="row avatar-btns">
                  <div class="col-md-3">
                    <button class="btn btn-primary btn-block avatar-save" type="submit">上传</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div><!-- /.modal -->

    <!-- Loading state -->
    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
  </div>
