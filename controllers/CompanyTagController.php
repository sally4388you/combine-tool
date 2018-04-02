<?php

namespace app\controllers;
use app\models\CompanyTag;

class CompanyTagController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	//first read if there is cache
        if(\Yii::$app->cache->exists('all_company_tag')){
            $retn_json = \Yii::$app->cache->get('all_company_tag');
        }else{
            //no cache ,get from db
            $records = CompanyTag::find()->all();
            $retn = [];
            foreach ($records as $record) {
                $retn[] = $record['name'];
            }
            $retn_json = json_encode($retn);
            //cache 20 minutes ,20*60
            \Yii::$app->cache->set('all_company_tag',$retn_json,1200);
        }
    	return $retn_json;
    }

}
