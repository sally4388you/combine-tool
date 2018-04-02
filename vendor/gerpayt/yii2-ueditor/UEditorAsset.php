<?php

namespace gerpayt\yii2_ueditor;

use yii\web\AssetBundle;

class UEditorAsset extends AssetBundle
{
    public $sourcePath = '@vendor/gerpayt/yii2-ueditor/src';

//    public $depends = [
//        'yii\bootstrap\BootstrapPluginAsset'
//    ];

    public function init()
    {
        $this->js[] = 'ueditor.config.js';
        $this->js[] = YII_DEBUG ? 'ueditor.all.js' : 'ueditor.all.min.js';
        $this->js[] = 'lang/zh-cn/zh-cn.js';
    }

}
