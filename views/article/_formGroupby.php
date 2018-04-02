<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use gerpayt\yii2_ueditor\UEditorWidget;
use app\models\TradeType;
use app\models\TradeSubType;
use app\models\WorkCategory;
use app\models\CompanyTag;
/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $form yii\widgets\ActiveForm */


    //Cache read first
    //
    //2 days = 2*24*60*60
    $expire = 172800;
    //trade type cahe
    if(\Yii::$app->cache->exists('view_trade_type_array')){
        $trade_type_array = \Yii::$app->cache->get('view_trade_type_array');
    }else{
        $trade_types = TradeType::find()->all();
        $trade_type_array = [];
        foreach ($trade_types as $trade_type) 
            $trade_type_array[$trade_type->id] = $trade_type->name;
        \Yii::$app->cache->set('view_trade_type_array',$trade_type_array,$expire);
    }

    //trade sub type cache
    if(\Yii::$app->cache->exists('view_trade_sub_type_array')){
        $trade_sub_type_cache = \Yii::$app->cache->get('view_trade_sub_type_array');
        $trade_sub_type_array = $trade_sub_type_cache['trade_sub_type_array'];
        $trade_sub_type_trade_type_array =$trade_sub_type_cache['trade_sub_type_trade_type_array'];
    }else{
        $trade_sub_types = TradeSubType::find()->all();    
        $trade_sub_type_array = [];
        $trade_sub_type_trade_type_array =[];
        foreach ($trade_sub_types as $trade_sub_type) {
            $trade_sub_type_array[$trade_sub_type->id] =$trade_sub_type->name;
            $trade_sub_type_trade_type_array[$trade_sub_type->trade_type_id][] = ['id' => $trade_sub_type->id , 'name'=>$trade_sub_type->name];
        }
        \Yii::$app->cache->set('view_trade_sub_type_array',['trade_sub_type_array'=>$trade_sub_type_array,'trade_sub_type_trade_type_array'=>$trade_sub_type_trade_type_array],$expire);
    }

    //work category cahe
    if(\Yii::$app->cache->exists('view_work_category_array')){
        $work_category_array = \Yii::$app->cache->get('view_work_category_array');
    }else{
        $work_categories = WorkCategory::find()->orderBy('sort')->all();
        $work_category_array = [];
        foreach ($work_categories as $work_category) 
            $work_category_array[$work_category->id] = $work_category->name;
        \Yii::$app->cache->set('view_work_category_array',$work_category_array,$expire);
    }

    //company tag cahe
    if(\Yii::$app->cache->exists('view_company_tag_array')){
        $company_tag_array = \Yii::$app->cache->get('view_company_tag_array');
    }else{
        $company_tags = CompanyTag::find()->all();
        $company_tag_array = [];
        foreach ($company_tags as $company_tag) 
            $company_tag_array[$company_tag->id] = $company_tag->name;
        \Yii::$app->cache->set('view_company_tag_array',$company_tag_array,$expire);
    }
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>



<input name="groupby" value="1" type="hidden">
<hr/>
<div class="row">
    <div class="col-md-6">
        <h1 id="article_title"><?php echo $model->title?></h1>
        <hr/>
        <div id="article_info">
            <?php echo $model->info?>
        </div>
    </div><!--col-md-6-->
    
    <div class="col-md-6">

<div class="form-group">
    <label class="control-label">标题</label>
    <p class="form-control-static"><?php echo $model->title?></p>
</div>
<div class="form-group">
    <label class="control-label">来源链接</label>
    <p class="form-control-static"><a href="<?php echo $model->fromlink?>" target='_blank'><?php echo substr($model->fromlink,0,40),'...'?></a></p>
</div>
<div class="form-group">
    <label class="control-label">年份</label>
    <p class="form-control-static"><?php echo $model->year?></p>
</div>
<div class="pinned">
<!--category-->
<div class="form-group">
    <label class="control-label" >行业</label>
    <input type="hidden" name="trade_sub_types" value="">        
    <span id="span_categories" class="form-control dropdown-toggle" data-toggle="dropdown"></span>
    <div class="dropdown-menu dropdown dropdown-menu-wide dropdown-menu-multiselect animated fadeIn not-close" role="menu">
        <div class="selected-box category">
            <?php if($model->trade_sub_types):?>
            <?php if($model->trade_sub_types[0]->trade_sub_type_id == 0):?>
                <span class="btn category" data-id="0" data-name="通用资料">
                通用资料
                     <a href="javascript:void(0);">
                        <i class="fa fa-times"></i>
                     </a>
                </span>
            <?php else: foreach($model->trade_sub_types as $model_trade_sub_type):?>
                <span class="btn category" data-id="<?php echo $model_trade_sub_type->trade_sub_type_id?>" data-name="<?php echo $trade_sub_type_array[$model_trade_sub_type->trade_sub_type_id]?>">
                <?php echo $trade_sub_type_array[$model_trade_sub_type->trade_sub_type_id]?>
                     <a href="javascript:void(0);">
                        <i class="fa fa-times"></i>
                     </a>
                </span>
            <?php endforeach; endif;endif;?>
        </div>

        <div class="item-box">
            <div class="row">
                <h5 class="col-xs-2 text-success text-right remove padding right">通用资料</h5>
                <div class="col-xs-10 btn-fluid-list">
                    <span class="width-split-4">
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
                        <span class="width-split-4">
                            <a href="javascript:void(0);" class="btn category" data-id="<?php echo $trade_sub_type['id']?>" data-name="<?php echo $trade_sub_type['name']?>"><?php echo $trade_sub_type['name']?></a>
                        </span>
                <?php endforeach;?>
                </div>
            </div>
                 <hr/>
            <?php endforeach;?>
        </div>
    </div>
</div>
<!--/category-->

<!--Job-->
<div class="form-group">
    <label class="control-label">职位</label>
    <input type="hidden" name="work_categories" value="">        
    <span id="span_jobs" class="form-control dropdown-toggle" data-toggle="dropdown"></span>
    <div class="dropdown-menu dropdown dropdown-menu-wide dropdown-menu-multiselect animated fadeIn not-close" role="menu">
        <div class="selected-box job">
            <?php if($model->work_categories): foreach($model->work_categories as $model_work_category):?>
                <span class="btn job" data-id="<?php echo $model_work_category->work_category_id?>" data-name="<?php echo $work_category_array[$model_work_category->work_category_id]?>">
                <?php echo $work_category_array[$model_work_category->work_category_id]?>
                     <a href="javascript:void(0);">
                        <i class="fa fa-times"></i>
                     </a>
                </span>
            <?php endforeach; endif;?>
        </div>

        <div class="item-box btn-fluid-list">
            <?php foreach ($work_category_array as $work_category_id=>$work_category_name):?>
                    <span class="width-split-4">
                        <a href="javascript:void(0);" class="btn job" data-id="<?php echo $work_category_id?>" data-name="<?php echo $work_category_name?>"><?php echo $work_category_name?></a>
                    </span>
                    <?php
                    // '1'=>'生产/制造',
                    // '2'=>'工程',
                    // '3'=>'技术研发',
                    // '4'=>'技术支持',
                    // '5'=>'产品', 
                    // '6'=>'游戏策划',
                    // '7'=>'创意/设计',
                    // '8'=>'运营', => id 7

                    // '9'=>'金融',
                    // '10'=>'审计',
                    // '11'=>'财务', => id 1

                    // '12'=>'市场营销',
                    // '13'=>'商务拓展',
                    // '14'=>'贸易/进出口',
                    // '15'=>'渠道/分销',
                    // '16'=>'销售',
                    // '17'=>'采购', => id 25

                    // '18'=>'管理',
                    // '19'=>'质量管理',
                    // '20'=>'项目管理',
                    // '21'=>'物流/供应链',
                    // '22'=>'物业管理',
                    // '23'=>'人力资源',
                    // '24'=>'行政',
                    // '25'=>'公关',
                    // '26'=>'客户服务',
                    // '27'=>'编辑/文案',
                    // '28'=>'咨询/顾问',
                    // '29'=>'法律',
                    // '30'=>'翻译',
                    // '31'=>'培训',
                    // '32'=>'教师', => id 22

                    // '33'=>'医疗健康',
                    // '34'=>'艺术',
                    // '35'=>'科研',
                    // '36'=>'公务员',
                    // '37'=>'管理培训生',
                    // '38'=>'其他', => id 36
                    if( $work_category_id == 1 || $work_category_id == 7 || $work_category_id == 22 || $work_category_id == 25 || $work_category_id == 36 )
                        echo '<hr/>';
                    ?>
            <?php endforeach;?>
            </div>
        </div>
    </div>
<!--/Job-->

<!--Comptag-->
<div class="form-group comptags">
    <label class="control-label">公司标签</label>
    <input type="hidden" name="company_tags" value="">
    <div class="form-control">
        <div class="taglist">
           <?php if($model->company_tags): foreach($model->company_tags as $model_company_tag): ?>
                <span class="label label-info" data-name="<?php echo $company_tag_array[$model_company_tag->company_tag_id]?>"><?php echo $company_tag_array[$model_company_tag->company_tag_id]?> <i class="fa fa-times"></i></span>
            <?php endforeach; endif;?>
        </div>
        <input type="text" class="newtag">
    </div>
</div>
<!--/Comptag-->
    <span class="msgbox"><strong>请填写完整所有信息</strong></span>
    <input name="nextaction" value="" type="hidden">
<hr/>
    <div class="form-group">
        <?= Html::submitButton('保存并预览' ,['class' => 'btn btn-success','id'=>'preview']) ?>
        <?= Html::submitButton('保存并处理下一篇' ,['class' => 'btn btn-success','id'=>'next']) ?>
        <?= Html::submitButton('更新正文' ,['class' => 'btn btn-primary','id'=>'update']) ?>
        <?= Html::Button('删除并跳转下一篇' ,['class' => 'btn btn-danger','id'=>'delete','data-id'=>$model->id]) ?>
        <?= Html::Button('清空公司标签' ,['class' => 'btn btn-success','id'=>'clear_company_tag']) ?>
    </div>
</div><!--.pinned-->

    </div><!--/col-md-6-->
</div><!--/row-->



    <?php ActiveForm::end(); ?>
    <div id="btn_tag" class="hide" data-toggle="tooltip" data-placement="top" title="添加标签" style="cursor:pointer;position:absolute" >
		<span class="fa-stack fa-lg">
		  <i class="fa fa-circle-thin fa-stack-2x"></i>
		  <i class="fa fa-plus fa-stack-1x"></i>
		</span>
	</div>
</div>
