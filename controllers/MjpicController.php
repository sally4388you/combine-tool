<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FilltradeController implements the CRUD actions for CompanyTag model.
 */
class MjpicController extends Controller
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
        $filenames = [];
        $path = scandir("../web/images/mjpic_s");
        
        for ($i = 2; $i < count($path); $i ++) {
            $filenames[] = $path[$i];
        }

        return $this->render('index', ['filenames' => $filenames]);
    }

    public function actionProxy() {
        $attr = Yii::$app->request->post('attr', 'http://img5.imgtn.bdimg.com/it/u=3510384511,555111286&fm=11&gp=0.jpg');
        if ($attr != null) {

            $header[] ='Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
            $header[] ='Accept-Encoding:gzip, deflate, sdch';
            $header[] ='Accept-Language:zh-CN,zh;q=0.8';
            $header[] ='Cache-Control:no-cache';
            $header[] ='Connection:keep-alive';
            $header[] ='Pragma:no-cache';
            $header[] ='User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.155 Safari/537.36';
            $header[] ='Upgrade-Insecure-Requests:1';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $attr);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);
            curl_close($ch);

            $filename = '../web/images/temp_mj.png';
            $fp = fopen($filename, "w");
            if (!fwrite($fp, $data)) echo "图片缓存失败";
            fclose($fp);

            echo "suc";
        }
    }

    public function actionPictureshot() {
        date_default_timezone_set ('Asia/Shanghai');
        $avatar_data = Yii::$app->request->post('avatar_data', null);
        if ($avatar_data == null) return ;

        $data = json_decode(stripslashes($avatar_data));//json_decode(stripslashes($avatar_data));
        $src = '../web/images/temp_mj.png';
        $dst = '../web/images/mjpic/' . date("YmdHis") . '.png';
        $msg = null;

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
        $dst_img_w = 450;
        $dst_img_h = 270;

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

        // Add a thumbnail! Author: qibuer
        $thumbnail = imagecreatetruecolor(200, 120);
        imagecopyresampled($thumbnail, $dst_img, 0, 0, 0, 0, 200, 120, $dst_img_w, $dst_img_h);
        imagepng($thumbnail, '../web/images/mjpic_s/' . date("YmdHis") . '.png', 1);

        imagedestroy($src_img);
        imagedestroy($dst_img);

        $response = array(
            'state'  => 200,
            'message' => $msg,
            'result' => !empty($data) ? $dst : $src
        );

        echo json_encode($response);
    }

    public function actionDelete() {
        $picName = Yii::$app->request->post('picName', null);
        $path = "../web/images/mjpic/" . $picName;
        $path_s = "../web/images/mjpic_s/" . $picName;
        if (file_exists($path)) {
            if (!@unlink($path)) echo "error";
        }
        if (file_exists($path_s)) {
            if (!@unlink($path_s)) echo "error";
            else echo "suc";
        }
    }
}
