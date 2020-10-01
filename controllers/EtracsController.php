<?php

namespace app\controllers;

use app\models\EtracsRetrospective;
use yii\web\Controller;
use yii\data\Pagination;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\db\Exception;
use Yii;

class EtracsController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;


    /**
     * behavior
     * @return [type] [description]
     */
    public function behaviors() {
        return [];
    }


    public function actionIndex()
    {
        $this->layout = 'blank';

        $model = new EtracsRetrospective();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->render('congrats');

        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

}
