<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::getAlias('@web'); ?>/css/main.css" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
     NavBar::begin([
                'brandLabel' => 'Start Pagina',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
     
    
    echo Nav::widget([
        
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'Gebruikers', 'url' => ['/user'], 'visible'=>!Yii::$app->user->isGuest],    //Yii::$app->user->getIdentity('user')],
            ['label' => 'Afbeeldingen', 'url' => ['/image'], 'visible'=>!Yii::$app->user->isGuest],   //Yii::$app->user->getIdentity('moderator')],
            ['label' => 'Audio', 'url' => ['/audio'], 'visible'=>!Yii::$app->user->isGuest],  //Yii::$app->user->getIdentity('user')],
            ['label' => 'Documenten', 'url' => ['/document'], 'visible'=>!Yii::$app->user->isGuest],  //Yii::$app->user->getIdentity('user')],
            ['label' => 'Inloggen', 'url' => ['/site/login'], 'visible'=>Yii::$app->user->isGuest],
             Yii::$app->user->isGuest ?
            ['label' => 'Uitloggen', 'url' => ['/site/login'], 'visible'=>!Yii::$app->user->isGuest] :
            ['label' => 'Uitloggen ('.Yii::$app->user->identity->username.')', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']]
            /*
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link']
                )
                . Html::endForm()
                . '</li>'
            )
             * 
             */
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
