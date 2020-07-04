<?php

namespace app\controllers;

use app\models\CompanyTag as Combine;
// use app\models\Exam;
// use app\models\Article;
use app\models\CompanyTrade;
// use app\models\ArticleCompanyTag;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\data\Pagination;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\db\Exception;
use Yii;

class CombineController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * behavior
     * @return [type] [description]
     */
    public function behaviors() {
        return [
        //     'access' => [
        //         'class' => AccessControl::className(),
        //         'rules' => [
        //             [
        //                 'allow' => true,
        //                 'roles' => ['@'],
        //             ],
        //         ],
        //     ],
        ];
    }
    
    public function actionIndex()
    {
        $count = 0;
        $allData = Combine::find()->where(['isdelete' => 0])->orderBy('name')->all();

        $isGroup = [];
        $isCombined = [];
        $groupName = [];
        foreach ($allData as $data) {
            //If this is a name of one combined label. But Maybe it's not combined with others.
        	if ($data['group_id'] == $data['id']) {
        		$groupName[$data['id']] = $data['name'];//remember this label's name and id in an array
                $count ++;
        	} else {
                $isGroup[$data['group_id']] = 1;//Judge whether this is combined with others.
        	}
            if ($data['group_id'] == 0) {
                $groupName[$data['id']] = $data['name'];
                $isCombined[$data['id']] = 1;
            }
        }

        return $this->render('index', [
            'groupName' => $groupName,
            'isCombined' => $isCombined,
            'isGroup' => $isGroup,
            'count' => $count,
        ]);
    }

    public function actionMerge() {
        // $target = Yii::$app->request->post('target', 2000);
        // $el = Yii::$app->request->post('el', 6);
        // if ($target == '' || $el == '') {
        //     return 'error';
        // } else {
        //     //merge
        //     $combines = Combine::updateAll(['group_id' => $target], ['or', ['group_id' => $el], ['id' => $el], ['id' => $target]]);
        //     //change trade's type
        //     $trade_old = CompanyTrade::findOne(['group_id' => $el]);
        //     $trade_new = CompanyTrade::findOne(['group_id' => $target]);
        //     if ($trade_old != null) {
        //         if ($trade_new == null) {
        //             $trade_old->group_id = $target;
        //             $trade_old->update();
        //         }
        //     }
        //     //rename picture's name
        //     $oldname = '../web/logo/' . $el . '.png';
        //     $newname = '../web/logo/' . $target . '.png';
        //     if (file_exists($oldname)) {
        //         if (file_exists($newname)) {
        //             @unlink ($oldname);
        //         } else {
        //             rename($oldname, $newname);
        //         }
        //     }

        //     $allData = Combine::find()->select(['id', 'name'])->where("`group_id` = $target AND `id` != $target")->all();
        //     Yii::$app->response->format = Response::FORMAT_JSON;
        //     return ['group' => $allData];
        // }
    }

    public function actionDelt() {
        // $id = Yii::$app->request->post('id', null);//this is the id of trash element 
        // $combine = Combine::findOne(['id' => $id]);
        // $combine->group_id = $combine->id;
        // if ($combine->update()) {
        //     echo "success";
        // } else {
        //     echo 'error';
        // }
    }

    public function actionRename() {
        // $id = Yii::$app->request->post('id', null);
        // $name = Yii::$app->request->post('name', null);
        // $rename_d = Yii::$app->request->post('rename_d', false);
        // $initId = 0;
        // $remainId = 0;
        // if ($name != null) {
        //     $renameTag = Combine::findOne(['id' => $id]);//要改的
        //     $isRename = Combine::findOne(['name' => $name]);//重名的

        //     if ($renameTag->group_id == 0) {
        //         $renameTag->group_id = $renameTag->id;
        //     }

        //     if (!empty($isRename) && $renameTag->id != $isRename->id && $renameTag->group_id != $isRename->group_id) {
        //         if ($rename_d == 'false' || $rename_d == false) {
        //             echo "Duplicate name exists.";
        //             return ;
        //         }
        //         if ($isRename->group_id == 0) {//If repeat part is a new tag

        //             $initId = $isRename->id;//重名标签被隐藏
        //             $remainId = $id;

        //             $isRename->group_id = $renameTag->group_id;
        //             $isRename->name = $isRename->name."(2)";
        //             if (!$isRename->update()) {
        //                 $this->error("Fail to rename.");
        //                 return ;
        //             }

        //             $renameTag->name = $name;
        //             if (!$renameTag->update()) {
        //                 $this->error("Fail to rename.");
        //                 return ;
        //             }
        //         } else {
        //             $initId = $id;//要改的标签被隐藏
        //             $remainId = $isRename->id;

        //             $combines = Combine::updateAll(['group_id' => $isRename->group_id], ['group_id' => $renameTag->group_id]);
        //         }
        //         // $this->delete($initId, $remainId);
        //         if ($isRename->id != $isRename->group_id) {
        //             //for hidden tags' id;= =Don't want to care for it anymore
        //             $remainId = $isRename->group_id;
        //         }
        //         echo '{remainId:"'.$remainId.'", initId:"'.$initId.'"}';
        //     } else {
        //         $renameTag->name = $name;
        //         if (!$renameTag->update()) {
        //             $this->error("Rename cannot be the same value as before.");
        //             return ;
        //         }
        //     }
        // }
    }

    /*
    **   actions in searchBox
    */
    public function actionSearch() {
        $id = Yii::$app->request->get('id', null);
        $allData = Combine::find()->select(['id', 'name'])->where("`group_id` = $id AND `id` != $id")->all();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['group' => $allData];
    }

    public function actionAddtag() {
        // $name = Yii::$app->request->post('name', null);
        // $combine = new Combine();
        // $combine->name = $name;
        // $combine->group_id = 0;
        // if ($combine->insert()) {
        //     echo $combine->id;
        // } else {
        //     echo 'error';
        // }
    }

    /*
    **   actions in detailBox
    */
    public function actionReturn() {
        $id = Yii::$app->request->post('id', null);//this is the id of return element
        $combine = Combine::findOne(['id'=>$id]);
        echo $combine->group_id;
        
        $combine->group_id = $combine->id;
        $combine->update();
    }

    /*
    **   actions in trashBox and trashList's index
    */
    public function actionIntotrash() {
        // $id = Yii::$app->request->post('id', null);//this is the id of trash element        
        // $isDelete = Yii::$app->request->post('isDelete', 0);//0 is out of trash; 1 is into trash
        // $combine = Combine::findOne(['id' => $id]);
        // $combine->isdelete = intval($isDelete);
        // if ($isDelete == 0) $combine->group_id = 0;
        // if ($combine->update()) {
        //     echo "success";
        // } else {
        //     echo 'error';
        // }
    }

    public function actionTrashList() {
        $allData = Combine::find()->where(['isdelete' => 1])->orderBy('name')->all();

        $isGroup = [];
        $groupName = [];
        foreach ($allData as $data) {
            if ($data['group_id'] == $data['group_id']) {
                $groupName[$data['id']] = $data['name'];
            } else {
                $isGroup[$data['group_id']] = 1;
            }
        }

        return $this->render('trashList', [
            'groupName' => $groupName,
            'isGroup' => $isGroup,
        ]);
    }

    // public function delete($initId, $remainId) {
    //     $articleIds = [];
    //     $repeatIds = [];
    //     $connection = Yii::$app->db;
    //     $transaction = $connection->beginTransaction();
    //     try {
    //         Combine::findOne(['id' => $initId])->delete();
    //         // Exam::updateAll(['company_tag_id' => $remainId], ['company_tag_id' => $initId]);
    //         // ArticleCompanyTag::updateAll(['company_tag_id' => $remainId], ['company_tag_id' => $initId]);
    //         $transaction->commit();
    //     } catch(Exception $e){
    //         $transaction->rollBack();
    //         echo $e;
    //         return ;
    //     }

    //     $transaction = $connection->beginTransaction();
    //     $ArticleCompanyTags = ArticleCompanyTag::find()->where(['company_tag_id' => $remainId])->all();
    //     foreach ($ArticleCompanyTags as $tag) {
    //         $articleIds[$tag->id] = $tag->article_id;
    //     }
    //     asort($articleIds);
    //     while (list($key, $val) = each($articleIds)) {
    //         if ($val == current($articleIds)) {
    //             next($articleIds);
    //             $repeatIds[] = $key;
    //         }
    //     }
    //     try {
    //         ArticleCompanyTag::deleteAll(['id' => $repeatIds]);
    //         $transaction->commit();
    //     } catch(Exception $e){
    //         $transaction->rollBack();
    //         echo $e;
    //         return ;
    //     }
    // }

    public function error($text) {
        $error = $text;
        echo $error;
    }

    // public function actionGetmj() {
    //     $id = Yii::$app->request->post('id', null);
    //     $exams = Exam::find()->select('id,work_name')->where(['company_tag_id' => $id])->all();
    //     $mjs_id = (new Query())
    //         ->select('article_id')
    //         ->from('mjfx_article_company_tag')
    //         ->where(['company_tag_id' => $id])->all();
    //     $mjIds = [];
    //     foreach ($mjs_id as $mj) {
    //         $mjIds[] = $mj['article_id'];
    //     }
    //     $mjs = Article::find()->select('id,title')->where(['id' => $mjIds])->all();
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     return [
    //         'exams' => $exams,
    //         'mjs' => $mjs,
    //     ];
    // }

}

?>