<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Exam;
use app\models\ExamWorkCategory;
use app\models\CompanyTag;
use app\models\QuestionAnswer;
/**
 * restful controller
 */
class ExamController extends \yii\web\Controller
{
    /**
     * behavior
     * @return [type] [description]
     */
    public function behaviors() {
        return [
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

    // public $enableCsrfValidation = false;
    /**
     * action for list
     * @return [type] [description]
     */
    public function actionIndex()
    {
        $isAjax      = Yii::$app->request->get('isajax',null);
        if(is_null($isAjax))
            return $this->renderFile('@app/views/exam/index.php');

        $start      = Yii::$app->request->get('start',0);
        $length     = Yii::$app->request->get('length',50);
        $sort_by    = Yii::$app->request->get('sort_by','id');
        $sort_order = Yii::$app->request->get('sort_order','desc');
    
        $data =[];
        foreach (Exam::find()->with('company_tag')->offset($start)->limit($length)->orderBy("$sort_by $sort_order")->each() as $model)
            $data[] = [
            'id'=>$model->id,
            'company_tag'=>$model->company_tag->name,
            'work_name'=>$model->work_name,
            'year'=>$model->year,
            'created_at'=>$model->created_at
            ];
        $count = Exam::find()->count();
        $retn =['count'=>$count,'data'=>$data];
        return json_encode($retn);
    }

    /**
     * action for show a single exam
     * @return [type] [description]
     */
    public function actionView($id)
    {
        $examModel = Exam::findOne($id);
        
        if( is_null($examModel) )
            return Yii::$app->response->statusCode = 404;

        $examWorkCategories = $examModel->exam_work_categories;
        $questionAnswers = $examModel->question_answers;
        // $yeat = $examModel->year;
        $retn = [
            'id' => $examModel->id ,
            'company_tag' => CompanyTag::findOne(['id'=>$examModel->company_tag_id])->name,
            'work_name' => $examModel->work_name,
            'year'=> $examModel->year
        ];

        foreach ($examWorkCategories as $examWorkCategory) 
            $retn['work_category'][] = $examWorkCategory->work_category_id;

        foreach ($questionAnswers as $questionAnswer) 
            $retn['question_answer'][] = [
                    'question' => $questionAnswer->question,
                    'answer'   => $questionAnswer->answer
            ];

        return json_encode($retn);
    }

    /**
     * save a form , for new exam
     * @return [type] [description]
     */
    public function actionCreate()
    {
        //fields : company_tag , work_name , year , work_category , question_answer[]
        //company_tag , work_name , year is 4 Exam table
        //work_category is 4 ExamWorkCategory table
        //question_answer is 4 QuestionAnswer table
        
        $data = json_decode( Yii::$app->request->getRawBody() ,true);

        if( empty($data) )
            return Yii::$app->response->statusCode = 400;
        //data exists then we verify
        $companyTag     =   isset($data['company_tag'])     ? $data['company_tag'] : null;
        $workName       =   isset($data['work_name'])       ? $data['work_name'] : null;
        $year           =   isset($data['year'])            ? $data['year'] : null;
        $workCategory   =   isset($data['work_category'])   ? $data['work_category'] : null;
        $questionAnswer =   isset($data['question_answer']) ? $data['question_answer'] : null;

        if( empty($companyTag) || empty($workName) || empty($year) || !is_numeric($year) 
            ||empty($workCategory) || !is_array($workCategory) 
            || empty($questionAnswer) || !is_array($questionAnswer) )
            return Yii::$app->response->statusCode = 400;


        //after verify data

        //first get or save company tag
        $companyTag = strtolower($companyTag);
        $companyTagModel = CompanyTag::findOne(['name'=>$companyTag]);

        if( is_null($companyTagModel) ){
            $companyTagModel = new CompanyTag();
            $companyTagModel->name = $companyTag;
            $companyTagModel->save(); 
        }

        $examModel = new Exam();
        $examModel->company_tag_id = $companyTagModel->id;
        $examModel->work_name = $workName;
        $examModel->year = $year;
        $examModel->save();

        $examId = $examModel->id;
        
        $this->exam_work_category_action( $examId , $workCategory);
        $this->question_answer_action( $examId , $questionAnswer );

        return json_encode(['id'=>$examId]);
    }

    /**
     * update a form , for a exists one
     * @return [type] [description]
     */
    public function actionUpdate($id)
    {
        //fields : company_tag , work_name , year , work_category , question_answer[]
        //company_tag , work_name , year is 4 Exam table
        //work_category is 4 ExamWorkCategory table
        //question_answer is 4 QuestionAnswer table
        
        $data = json_decode( Yii::$app->request->getRawBody() ,true);

        if( empty($data) )
            return Yii::$app->response->statusCode = 400;
        //data exists then we verify
        $companyTag     =   isset($data['company_tag'])     ? $data['company_tag'] : null;
        $workName       =   isset($data['work_name'])       ? $data['work_name'] : null;
        $year           =   isset($data['year'])            ? $data['year'] : null;
        $workCategory   =   isset($data['work_category'])   ? $data['work_category'] : null;
        $questionAnswer =   isset($data['question_answer']) ? $data['question_answer'] : null;

        if( empty($companyTag) || empty($workName) || empty($year) || !is_numeric($year) 
            ||empty($workCategory) || !is_array($workCategory) 
            || empty($questionAnswer) || !is_array($questionAnswer) )
            return Yii::$app->response->statusCode = 400;


        //after verify data
        
        //first get the model
        $examModel = Exam::findOne($id);
        if( is_null($examModel) ) return  Yii::$app->response->statusCode = 404;
        //then get or save company tag
        $companyTag = strtolower($companyTag);
        $companyTagModel = CompanyTag::findOne(['name'=>$companyTag]);

        if( is_null($companyTagModel) ){
            $companyTagModel = new CompanyTag();
            $companyTagModel->name = $companyTag;
            $companyTagModel->save(); 
        }

        $examModel->company_tag_id = $companyTagModel->id;
        $examModel->work_name = $workName;
        $examModel->year = $year;
        $examModel->save();
        
        $this->exam_work_category_action( $id , $workCategory,false);
        $this->question_answer_action( $id , $questionAnswer ,false);
        
        return json_encode(['id'=>$id]);
    }


    /**
     * delete a exist one
     * @return [type] [description]
     */
    public function actionDelete($id)
    {
        //first get the model
        $examModel = Exam::findOne($id);
        if( is_null($examModel) ) return  Yii::$app->response->statusCode = 404;
        
        ExamWorkCategory::deleteAll('exam_id=:exam_id',[':exam_id'=>$id]);
        QuestionAnswer::deleteAll('exam_id=:exam_id',[':exam_id'=>$id]);

        $examModel->delete();
    
    }


    private function exam_work_category_action($exam_id,$new_record,$is_new_record=true){
        //if it's new record ,we delete all old record first
        if( !$is_new_record )
            ExamWorkCategory::deleteAll('exam_id=:exam_id',[':exam_id'=>$exam_id]);
        //insert new record
        ExamWorkCategory::batch_insert($exam_id,$new_record);
    }

    private function question_answer_action($exam_id,$new_record,$is_new_record=true){
        //if it's new record ,we delete all old record first
        if( !$is_new_record )
            QuestionAnswer::deleteAll('exam_id=:exam_id',[':exam_id'=>$exam_id]);
        //insert new record
        QuestionAnswer::batch_insert($exam_id,$new_record);
    }
}
