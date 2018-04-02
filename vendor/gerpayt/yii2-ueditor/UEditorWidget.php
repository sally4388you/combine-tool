<?php

namespace gerpayt\yii2_ueditor;

use yii\helpers\ArrayHelper;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;
use gerpayt\yii2_ueditor\UEditorAsset;

class UEditorWidget extends InputWidget
{
    public $height = 300;

    public function run()
    {
        $options = $this->options;

        if (isset($options['id'])) {
            $id = $options['id'];
        } else {
            $id = 'editor';
        }

        if (isset($options['name'])) {
            $name = $options['name'];
        } elseif ($this->hasModel()) {
            $name = BaseHtml::getInputName($this->model, $this->attribute);
        } else {
            $name = $this->name;
        }

        if (isset($options['value'])) {
            $value = $options['value'];
        } elseif ($this->hasModel()) {
            $value = BaseHtml::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        echo Html::beginTag('script', ['id'=>$id, 'type'=>'text/plain', 'name' => $name, 'style' => "height:{$this->height}"]);
        echo $value;
        echo Html::endTag('script');

        if(!isset($options['config'])) {
            $options['config'] = [];
        }

        $ueditorConfig = ArrayHelper::merge(
            [
                'serverUrl' => Url::to(['ueditor/controller']),
            ],
            $options['config']
        );
        $config = Json::encode($ueditorConfig);

        $view = $this->getView();
        UEditorAsset::register($view);
        $view->registerJs("var ue = UE.getEditor('{$id}', {$config});");
    }

}
