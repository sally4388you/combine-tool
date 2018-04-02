<?php

namespace app\controllers;

use Yii;
use app\models\CompanyTag;
use app\models\CompanyTrade;
use app\models\TradeType;
use app\models\TradeSubType;
use yii\db\Query;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FilltradeController implements the CRUD actions for CompanyTag model.
 */
class FilltradeController extends Controller
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

    /**
     * Lists all CompanyTag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CompanyTag::find()
                ->select('group_id')
                ->distinct()
                ->where('group_id != 0 AND isdelete != 1')
                ->with('company_trade', 'company_tag'),
            'key' => 'group_id',
            'pagination' => ['pageSize' => 30],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProxy($addr){
        $file = file_get_contents($addr);
        return $file;
    }


    /**
     * Updates an existing CompanyTag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        date_default_timezone_set ('Asia/Shanghai');

        $tag = CompanyTag::findOne($id);
        $detail = $tag->getCompany_trade()->all();
        $combines = $tag->getCompany_tag()->all();

        if (!isset($detail[0])) {//if company_trade doesn't have this company's trade information
            $companyTrade = new CompanyTrade();
            $companyTrade->group_id = $tag->id;
            $companyTrade->time = date('Y-m-d H:i:s');
            if ($companyTrade->insert()) {
                $detail = $tag->getCompany_trade()->all();
            }
            else throw new Exception("Can't create new company's trade information", 1);
        }

        //<--spider for company's trade information
        //get header information
        $url = "http://so.dajie.com/corp/search?corpsearch=4";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt');
        $cookie = curl_exec($ch);
        curl_close($ch);

        //post header information
        $search = rawurlencode($tag->name);
        $url = "http://so.dajie.com/corp/ajax/search/filter?corpsearch=0&pagereferer=http%3A%2F%2Fso.dajie.com%2Fcorp%2Fsearch%3Fkeyword%3D%25E4%25BA%25A4%25E9%2580%259A%26_CSRFToken%3D&ajax=1&page=1&order=0&keyword=".$search."&query=1&from=auto";

        $header[] ='Accept:application/json, text/javascript, */*; q=0.01';
        $header[] ='Accept-Encoding:gzip, deflate, sdch';
        $header[] ='Accept-Language:zh-CN,zh;q=0.8';
        $header[] ='Cache-Control:no-cache';
        $header[] ='Connection:keep-alive';
        $header[] ='Host:so.dajie.com';
        $header[] ='Pragma:no-cache';
        $header[] ='Referer:http://so.dajie.com/corp/search?corpsearch=4';
        $header[] ='User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36';
        $header[] ='X-Requested-With:XMLHttpRequest';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt'); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        $data = curl_exec($ch);
        curl_close($ch);
        //--end spider-->

        //match information
        $pattern = '/listImgHref":"([^"]*)"[^{]*infoIndustry":"([^"]*)"[^{]*listTitle":"([^"]*)"[^{]*listImgSrc":"([^"]*)"/';
        preg_match_all($pattern, $data, $tradeInfo);
        foreach( $tradeInfo[4] as $key => $tmp ){
            if( $tmp == 'http://fs1.dajie.com/corplogo/50x50.png' )
                continue;
            $tradeInfo[4][$key] = '/filltrade/proxy?addr=' . $tmp;
        }


        //--------get tradetypes---------------//
        //get trade first type
        $trade_types = TradeType::find()->all();
        $trade_type_array = [];
        foreach ($trade_types as $trade_type) 
            $trade_type_array[$trade_type->id] = $trade_type->name;

        //get trade subtype
        $trade_sub_types = TradeSubType::find()->all();
        $trade_sub_type_array = [];
        $trade_sub_type_trade_type_array =[];
        foreach ($trade_sub_types as $trade_sub_type) {
            $trade_sub_type_array[$trade_sub_type->id] =$trade_sub_type->name;
            $trade_sub_type_trade_type_array[$trade_sub_type->trade_type_id][] = ['id' => $trade_sub_type->id , 'name'=>$trade_sub_type->name];
        }

        //return
        if ($tag->load(Yii::$app->request->post()) && $tag->save()) {
            return $this->redirect(['view', 'id' => $tag->id]);
        } else {
            return $this->render('update', [
                'id' => $id,
                'model' => $tag,
                'detail' => $detail,
                'combines' => $combines,
                'tradeInfo' => $tradeInfo,
                'trade_type_array' => $trade_type_array,
                'trade_sub_type_array' => $trade_sub_type_array,
                'trade_sub_type_trade_type_array' => $trade_sub_type_trade_type_array,
            ]);
        }
    }

    /**
     * Finds the CompanyTag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CompanyTag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionSubmit()
    {
        date_default_timezone_set ('Asia/Shanghai');
        $id = Yii::$app->request->get('id', 1040);
        $next = Yii::$app->request->post('next', 1);
        $trade = Yii::$app->request->post('trade_sub_types', null);
        $trade = preg_replace('/\[|\]/', '', $trade);
        $trade_ids = explode(',', $trade);
        $time = date('Y-m-d H:i:s');

        $companyTrade = CompanyTrade::findOne(['group_id' => $id]);
        $companyTrade->type = implode('+', $trade_ids);
        $companyTrade->author = Yii::$app->user->identity->username;
        $companyTrade->time = $time;
        $companyTrade->update();

        if ($next == 1) {
            $all = CompanyTag::find()
                ->select('group_id')
                ->distinct()->all();
            foreach ($all as $key => $sg) {
                if ($sg->group_id == $id) {
                    $id = $all[$key+1]->group_id;
                    break;
                }
            }
        }
        $this->redirect('/filltrade/update?id='.$id);
    }

    public function actionGetimage() {
        $url = Yii::$app->request->post('url', null);
        preg_match('/\?addr=(.*)/', $url, $tmp);
        $url = (isset($tmp[1])) ? $tmp[1] : $url;
        if (preg_match('/sqs/', $url)) {
            $url = preg_replace('/sqs/', 'sq', $url);
        } else if (preg_match('/mm\./', $url)) {
            $url = preg_replace('/mm\./', 'b.', $url);
        } else if(preg_match('/m\./', $url)) {
            $url = preg_replace('/m\./', 'b.', $url);
        }
        echo $url;
        if ($url != null) {
            $filename = '../web/images/temp.png';
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
            $size = strlen($img);
            $fp = fopen($filename, "w");
            if (!fwrite($fp, $img)) echo "图片缓存失败";
            fclose($fp);
        }
    }

    public function actionPictureshot() {
        $data = Yii::$app->request->post('avatar_data', null);
        $id = Yii::$app->request->post('pic-id', null);
        if ($data == null) return ;

        $data = json_decode(stripslashes($data));
        $src = '../web/images/temp.png';
        $dst = '../web/images/logo/' . $id . '.png';
        $msg = null;

        if (file_exists($dst)) {
            @unlink ($dst);
        }

        $src_img = (exif_imagetype($src) == IMAGETYPE_PNG) ? imagecreatefrompng($src) : imagecreatefromjpeg($src);

        if (!$src_img) {
            $msg = "Failed to read the image file";
            return;
        }

        $size = getimagesize($src);
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height

        $src_img_w = $size_w;
        $src_img_h = $size_h;

        $tmp_img_w = $data -> width;
        $tmp_img_h = $data -> height;
        $dst_img_w = 100;
        $dst_img_h = 100;

        $src_x = $data -> x;
        $src_y = $data -> y;

        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
            $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
            $dst_x = -$src_x;
            $src_x = 0;
            $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
            $dst_x = 0;
            $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }

        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
            $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
            $dst_y = -$src_y;
            $src_y = 0;
            $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
            $dst_y = 0;
            $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }

        // Scale to destination position and size
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;

        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

        // Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);

        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        if ($result) {
            if (!imagepng($dst_img, $dst)) {
              $msg = "Failed to save the cropped image file";
            }
        } else {
            $msg = "Failed to crop the image file";
        }

        imagedestroy($src_img);
        imagedestroy($dst_img);

        $response = array(
            'state'  => 200,
            'message' => $msg,
            'result' => !empty($data) ? $dst : $src
        );

        echo json_encode($response);
    }

    public function actionRegroup() {
        $new = Yii::$app->request->post('new', null);
        $old = Yii::$app->request->post('old', null);
        if ($new == '' || $old == '') {
            return 'error';
        } else {
            $combines = CompanyTag::updateAll(['group_id' => $new], ['group_id' => $old]);
            //change trade's group_id
            $trade_old = CompanyTrade::findOne(['group_id' => $old]);
            $trade_new = CompanyTrade::findOne(['group_id' => $new]);
            if ($trade_old != null) {
                if ($trade_new == null) {
                    $trade_old->group_id = $new;
                    $trade_old->update();
                }
            }
            //change picture's name
            $oldname = '../web/images/logo/' . $old . '.png';
            $newname = '../web/images/logo/' . $new . '.png';
            if (file_exists($oldname)) {
                if (file_exists($newname)) {
                    @unlink ($oldname);
                } else {
                    rename($oldname, $newname);
                }
            }
            echo "suc";
        }
    }
}
