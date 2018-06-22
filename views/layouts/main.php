<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
Yii::$app->session['language'] = Yii::$app->request->get('lang', Yii::$app->session['language']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>

        <?php $this->beginBody() ?>
        <div class="wrap">
            <?php
            $lang = empty(Yii::$app->session['language']) ? 'en' : Yii::$app->session['language'];
            NavBar::begin([
                // 'brandLabel' => '面经管理',
                'brandLabel' => Yii::$app->params[$lang]['interview_exp'],
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            // echo '<a class="navbar-brand" href="/exams#/">笔试真题</a>';
            echo '<a class="navbar-brand" href="/exams#/">'. Yii::$app->params[$lang]['written_test'] .'</a>';
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => Yii::$app->params[$lang]['add_inter'], 'url' => ['/article/create']],
                    ['label' => Yii::$app->params[$lang]['classify'], 'url' => ['/article?groupby=1']],
                    ['label' => Yii::$app->params[$lang]['add_written'], 'url' => ['/exams#/new']],
                    Yii::$app->user->isGuest ?
                        ['label' => Yii::$app->params[$lang]['login'], 'url' => ['/site/login']] :
                        ['label' => Yii::$app->params[$lang]['logout'].' (' . Yii::$app->user->identity->username . ')',
                        'url' => ['/site/logout'],
                        'linkOptions' => ['data-method' => 'post']],
                        ['label' => Yii::$app->params[$lang]['language'], 'url' => '?lang='.($lang == 'en'?'zh-CN':'en')],
                ],
            ]);
            NavBar::end();
            ?>

            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?= $content ?>
            </div>
        </div>



        <?php $this->endBody() ?>
        <script src="/js/app.js"></script>
    </body>
</html>
<?php $this->endPage() ?>
