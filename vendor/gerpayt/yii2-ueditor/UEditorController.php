<?php
/**
 * Created by PhpStorm.
 * User: cf
 * Date: 15-4-4
 * Time: 下午11:53
 */
namespace gerpayt\yii2_ueditor;

use Yii;
use yii\web\Controller;
use yii\web\Response;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use company\filters\CompanyUserChecked;
use company\filters\CompanyInfoChecked;


class UEditorController extends Controller
{
    public $config = [];
    public $actionName = '';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => [
                    'identityClass' => 'company\models\profile\CompanyUser',
                ],
                'only' => ['complete'],
                'rules' => [
                    [
                        'actions' => ['complete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'CompanyUserChecked' => [
                'class' => CompanyUserChecked::className(),
                'user' => [
                    'identityClass' => 'company\models\profile\CompanyUser',
                ],
            ],
            'CompanyInfoChecked' => [
                'class' => CompanyInfoChecked::className(),
                'user' => [
                    'identityClass' => 'company\models\profile\CompanyUser',
                ],
            ],
        ];
    }

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->config = require("config.php");
        $this->actionName = Yii::$app->request->get('action');
    }

    public function beforeAction($action)
    {
        Yii::$app->request->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionController()
    {
        switch ($this->actionName) {
                /* 获取配置 */
            case 'config':
                $result = $this->config;
                break;

            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                $result = $this->actionUpload();
                break;

            /* 列出图片 */
            case 'listimage':
            /* 列出文件 */
            case 'listfile':
                $result = $this->actionList();
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->actionCrawler();
                break;

            default:
                $result = ['state'=> '请求地址出错'];
                break;
        }

        /* 输出结果 */
        $callback = Yii::$app->request->get('callback');
        if ($callback) {
            if (preg_match("/^[\w_]+$/", $callback)) {
                return $this->renderContent(htmlspecialchars($callback) . '(' . $result . ')');
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['state'=> 'callback参数不合法'];
            }
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionUpload()
    {
        /* 上传配置 */
        $base64 = "upload";
        switch ($this->actionName) {
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $this->config['imagePathFormat'],
                    "maxSize" => $this->config['imageMaxSize'],
                    "allowFiles" => $this->config['imageAllowFiles']
                );
                $fieldName = $this->config['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $this->config['scrawlPathFormat'],
                    "maxSize" => $this->config['scrawlMaxSize'],
                    "allowFiles" => $this->config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $this->config['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $this->config['videoPathFormat'],
                    "maxSize" => $this->config['videoMaxSize'],
                    "allowFiles" => $this->config['videoAllowFiles']
                );
                $fieldName = $this->config['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $config = array(
                    "pathFormat" => $this->config['filePathFormat'],
                    "maxSize" => $this->config['fileMaxSize'],
                    "allowFiles" => $this->config['fileAllowFiles']
                );
                $fieldName = $this->config['fileFieldName'];
                break;
        }

        /* 生成上传实例对象并完成上传 */
        $up = new UEditorUploader($fieldName, $config, $base64);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */

        /* 返回数据 */
        return $up->getFileInfo();

    }

    public function actionList()
    {
        /* 判断类型 */
        switch ($this->actionName) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $this->config['fileManagerAllowFiles'];
                $listSize = $this->config['fileManagerListSize'];
                $path = $this->config['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $this->config['imageManagerAllowFiles'];
                $listSize = $this->config['imageManagerListSize'];
                $path = $this->config['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = Yii::$app->request->get('size', $listSize);
        $start = Yii::$app->request->get('start', 0);
        $end = $start + $size;

        /* 获取文件列表 */
        //替换公司ID
        $path = str_replace("{cid}", Yii::$app->companyUser->cid, $path);

        $path = Yii::$app->params['UPLOAD_BASE_PATH'].'/'. (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = self::getFiles($path, $allowFiles);
        if (!count($files)) {
            return [
                "state" => "no match file",
                "list" => [],
                "start" => $start,
                "total" => count($files)
            ];
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }

        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        $result = [
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ];

        return $result;

    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param $allowFiles
     * @param array $files
     * @return array
     */
    protected static function getFiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    static::getFiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    public function actionCrawler()
    {
        /* 上传配置 */
        $config = array(
            "pathFormat" => $this->config['catcherPathFormat'],
            "maxSize" => $this->config['catcherMaxSize'],
            "allowFiles" => $this->config['catcherAllowFiles'],
            "oriName" => "remote.png"
        );
        $fieldName = $this->config['catcherFieldName'];

        /* 抓取远程图片 */
        $list = array();
        $source = Yii::$app->request->post($fieldName);
        if (!$source) {
            $source = Yii::$app->request->get($fieldName);
        }

        foreach ($source as $imgUrl) {
            $item = new UEditorUploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, array(
                "state" => $info["state"],
                "url" => $info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source" => htmlspecialchars($imgUrl)
            ));
        }

        /* 返回抓取数据 */
        return [
            'state'=> count($list) ? 'SUCCESS' : 'ERROR',
            'list'=> $list
        ];
    }

} 