<?php

namespace app\controllers;

use Yii;
use app\models\Article;
use app\models\CompanyTag;
use app\models\ArticleTradeSubType;
use app\models\ArticleWorkCategory;
use app\models\ArticleCompanyTag;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex() {
        //groupby eq 1 means we are now going to group by
        if(Yii::$app->request->get('groupby','') == 1){
            $dataProvider = new ActiveDataProvider([
                'query' => Article::find()->where(['groupby'=>null,'deleted_at'=>null]),
                 'pagination' => [
                      'pageSize' => 50,
                  ],
            ]);
            $dataProvider->sort->defaultOrder = ['time' => SORT_DESC];
            return $this->render('index', [
                    'dataProvider' => $dataProvider,
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Article::find()->where('deleted_at is null'),
            'pagination' => [
                  'pageSize' => 50,
            ],
        ]);
        $dataProvider->sort->defaultOrder = ['time' => SORT_DESC];
        return $this->render('index', [
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Article model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Article();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            // //get all relation json data
            // $categories = json_decode( Yii::$app->request->post('categories'),'' );
            // $jobs = json_decode( Yii::$app->request->post('jobs'),'' );
            // $comptags = json_decode(Yii::$app->request->post('comptags'),'' );
            // //insert 
            // if(is_array($categories) && ! empty($categories))
            //     $this->article_category_action($model->id,$categories);
            
            // if(is_array($jobs) && !empty($jobs))
            //     $this->article_job_action($model->id,$jobs);

            // if(is_array($comptags) && !empty($comptags))
            //     $this->article_comptag_action($model->id,$comptags);

            //show new create form
            if( Yii::$app->request->post('nextaction') == 'next' )
                return $this->redirect(['create']);
            //show view form
             return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                    'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {


        $groupby = Yii::$app->request->post('groupby','');

        $model = $this->findModel($id);
        //only when there is a field named groupby in the post field
        //that i will update the relation tables
        if($groupby == 1 ){
            //get all relation json data
            $trade_sub_types = json_decode( Yii::$app->request->post('trade_sub_types'),'' );
            $work_categories = json_decode( Yii::$app->request->post('work_categories'),'' );
            $company_tags = json_decode(Yii::$app->request->post('company_tags'),'' );
            //insert 
            if(is_array($trade_sub_types) && ! empty($trade_sub_types))
                $this->article_trade_sub_type_action($model->id,$trade_sub_types,false);
            
            if(is_array($work_categories) && !empty($work_categories))
                $this->article_work_category_action($model->id,$work_categories,false);

            if(is_array($company_tags) && !empty($company_tags))
                $this->article_company_tag_action($model->id,$company_tags,false);
            
            if( empty($model->groupby) || is_null($model->groupby) ){
                $model->groupby = Yii::$app->user->getIdentity()->username;
                $model->save();
            }

        }else{
        //no groupby so we update article fields

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //do nothing
            } else {
                return $this->render('update', [
                        'model' => $model,
                ]);
            }
        }

        //see if there is a nextaction field
        //
        // all possible value can be preview | next | update | //delete no delete allowed
        $next_action = Yii::$app->request->post('nextaction','preview');
        //only where field is next or delete we should also get the next article
        if(  $next_action == 'next'){

            $next = Article::find()->where("groupby is null and deleted_at is null and  id > ".$model->id)->limit(1)->one();
            if( !is_null($next) ){
                if( $groupby == 1)
                    return $this->redirect(['update', 'id' => $next->id,'groupby' => 1]);
                return $this->redirect(['update', 'id' => $next->id]);
            }
            //it's the last article
            throw new NotFoundHttpException('没有下一篇了');
        }
        //update 
        //update this article
        else if($next_action == 'update'){
            return $this->redirect(['update', 'id' => $model->id]);
        }
        //groupby
        else if($next_action == 'groupby'){
            return $this->redirect(['update', 'id' => $model->id,'groupby' => 1]);
        }
        //preview
        return $this->redirect(['view', 'id' => $model->id]);

    }

    /**
     * Deletes an existing Article model.
     * And also deletes all the relation records
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        //sort delete
        $this->findModel($id)->softDelete();
        
        //delete all relation table and it's record
        ArticleTradeSubType::deleteAll('article_id=:article_id',[':article_id'=>$id]);
        ArticleWorkCategory::deleteAll('article_id=:article_id',[':article_id'=>$id]);
        ArticleCompanyTag::deleteAll('article_id=:article_id',[':article_id'=>$id]);
        
        //there is netaction field
        //go to next page
        if(Yii::$app->request->post('nextaction','') == 'next'){
            $next = Article::find()->where("groupby is null and deleted_at is null and id > ".$id)->limit(1)->one();
            if( !is_null($next) ){
                if( Yii::$app->request->post('groupby') == 1)
                    return $this->redirect(['update', 'id' => $next->id,'groupby' => 1]);
                return $this->redirect(['update', 'id' => $next->id]);
            }
            throw new NotFoundHttpException('没有下一篇了');
        }
        return $this->redirect(['index']);
    }

    /**
     * Unit Test
     *
     * TODO:to be delete
     * @return [type] [description]
     */
    public function actionUnittest(){
        $work_categories = \app\models\WorkCategory::find()->orderby('sort')->asArray()->all();
        $retn = [];
        foreach ($work_categories as $work_category) {
            $retn['\''.$work_category['id'].'\''] = $work_category['name'];
        }
        return json_encode($retn);

    }

    /**
     * action 4 deal with article_trade_sub_type relation table
     * if the param $is_new_record is true 
     * it's recommend that is a new record ,so that we need not to 
     * delete the old record with this article id
     * else we need first delete the record with this article id 
     * 
     * @param  [type] $article_id [description]
     * @param  [type] $new_record [description]
     * @param  [type] $old_recore [description]
     * @return [type]             [description]
     */
    private function article_trade_sub_type_action($article_id,$new_record,$is_new_record=true){
        //if it's new record ,we delete all old record first
        if( !$is_new_record )
            ArticleTradeSubType::deleteAll('article_id=:article_id',[':article_id'=>$article_id]);
        //insert new record
        //whatever the subcategory is 0 or not, not effect
        ArticleTradeSubType::batch_insert($article_id,$new_record);
    }

    /**
     * action 4 deal with article_work_category relation table
     * if the param $is_new_record is true 
     * it's recommend that is a new record ,so that we need not to 
     * delete the old record with this article id
     * else we need first delete the record with this article id 
     * 
     * @param  [type] $article_id [description]
     * @param  [type] $new_record [description]
     * @param  [type] $old_recore [description]
     * @return [type]             [description]
     */
    private function article_work_category_action($article_id,$new_record,$is_new_record=true){
        //if it's new record ,we delete all old record first
        if( !$is_new_record )
            ArticleWorkCategory::deleteAll('article_id=:article_id',[':article_id'=>$article_id]);
        //insert new record
        ArticleWorkCategory::batch_insert($article_id,$new_record);
    }


    /**
     * action 4 deal with article_company_tag relation table
     * if the param $is_new_record is true 
     * it's recommend that is a new record ,so that we need not to 
     * delete the old record with this article id
     * else we need first delete the record with this article id 
     * 
     * @param  [type] $article_id [description]
     * @param  [type] $new_record [description]
     * @param  [type] $old_recore [description]
     * @return [type]             [description]
     */
    private function article_company_tag_action($article_id,$new_record,$is_new_record=true){
        //if it's new record ,we delete all old record first
        if( !$is_new_record )
            ArticleCompanyTag::deleteAll('article_id=:article_id',[':article_id'=>$article_id]);
        //insert new record
        $acModel = new ArticleCompanyTag();
        $acModel->article_id = $article_id;
        
        $record_array = [];
        $flush_cache =false;
        //first we should seek if the tag is already in the table ,
        //if not we should first insert it to the comptag table
        foreach ($new_record as $comptag_name) {
            $comptag_name = strtolower($comptag_name);
            $exist_record = CompanyTag::findOne(['name'=>$comptag_name]);
            //no such record then first insert this tag
            if( is_null($exist_record) ){
                $flush_cache = true;
                $exist_record = new CompanyTag();
                $exist_record->name = $comptag_name;
                $exist_record->save();
              }
              $record_array [] = $exist_record->id;
        }
        if($flush_cache)
            \Yii::$app->cache->delete('view_company_tag_array');
        //batch insert new record
        ArticleCompanyTag::batch_insert($article_id,$record_array);
        
    }


    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Article::findOne([
                'id'=>$id,
                'deleted_at'=>null
            ])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
