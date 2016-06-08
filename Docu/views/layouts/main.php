<?php

namespace app\views\layouts;

use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Button;
use app\models\Search;
use yii\bootstrap\Nav;
use app\assets\AppAsset;
use app\models\LoginForm;
use yii\web\View;
use Yii;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />
        <title><?php echo ''// yii\helpers\Html::encode($this->pageTitle);         ?></title>

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::getAlias('@web'); ?>/css/site.css" />
        <link rel="shortcut icon" href="<?php echo Yii::getAlias('@web/themes/dcu') ?>/assets/images/favicon.png" type="image/x-icon" />
        <?php
        $this->registerCssFile(Yii::getAlias('@web/css/site.css'));
        $this->registerCssFile(Yii::getAlias('@web/themes/dcu/assets/css/bootstrap.css'));
        $this->registerCssFile(Yii::getAlias('@web/themes/dcu/assets/css/bootstrap-responsive.min.css'));
        $this->registerCssFile(Yii::getAlias('@web/themes/dcu/assets/css/gbu.css'));
        $this->registerCssFile(Yii::getAlias('@web/themes/dcu/assets/css/font-awesome.css'));

        $this->registerJs(Yii::getAlias('@web/themes/dcu/assets/js/modernizr.custom.js'), View::POS_HEAD);
        $this->registerJs(Yii::getAlias('@web/themes/dcu/assets/js/bootstrap.min.js'), View::POS_END);
        $this->registerJs(Yii::getAlias('@web/themes/dcu/assets/js/main.js'), View::POS_END);
        $this->head();
        ?>
    </head>
    <body>
        <?php $this->beginBody(); ?>
        <span class="striept"></span>
        <div id="wrap">
            <div class="container" id="page">
                <div class="row header">
                    <div class="span7">
                        <a href=<?php Yii::$app->homeUrl ?>><img src="<?php echo Yii::getAlias('@web'); ?>/themes/dcu/assets/images/documentatiecentrum-urk.png" /></a>
                    </div>
                    <div class="span5 login-form">
                        <?php if (Yii::$app->user->isGuest): ?>
                            <div class="form">
                                <?php
                                $form = ActiveForm::begin([
                                            'action' => yii\helpers\Url::to(['site/login']),
                                            'id' => 'login-form',
                                            //   'type' => 'inline',
                                            'enableClientValidation' => true,
                                                // 'clientOptions' => [
                                                //      'validateOnSubmit' => true,
                                                // ],
                                ]);
                                ?>

                                <?php $model = new LoginForm; ?>
                                <?= $form->field($model, 'username')->textInput(['class' => 'span2']) ?>
                                <?= $form->field($model, 'password')->passwordInput(['class' => 'span2']) ?>
                                <?= Html::submitButton('Inloggen', ['class' => 'btn btn-primary']) ?>
                                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                                <?php ActiveForm::end(); ?>
                            </div>
                        <?php else: ?>
                            <ul class="inline">
                                <li>Je bent inlogd als: <?php echo Yii::$app->user->identity->username; ?> </li>
                                <li>
                                    <?php
                                    //      echo Button::Widget([
                                    //         'label' => 'Uitloggen',
                                    //         'options' => ['class' => 'btn btn-primary'],
                                    //             'url' => Url::toRoute(['site/logout']),
                                    //  'icon' => 'off white',
                                    //     ]);
                                    //    
                                    //  echo Html::a('Uitloggen', Url::to(['site/logout']), ['class' => 'btn btn-primary']);
                                    ?>
                                    <?= Html::a('Logout', ['site/logout'], ['data' => ['method' => 'post']]) ?>               
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="span12 zoeken">
                        <div class="input-append">
                            <?php
                            if (Yii::$app->getRequest()->getQueryParam('q')) {
                                $keyword = Yii::$app->getRequest()->getQueryParam('q');
                            } else {
                                $keyword = '';
                            }

                            echo Html::beginForm(['search/results'], 'get', ['class' => 'zoekbalk form-search']);

                            $model = new Search();

                            echo Html::textInput('q', $keyword, ['class' => 'searchInput', 'placeholder' => 'Zoekopdracht']);

                            echo '<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>';

                            echo Html::endForm();
                            ?>
                        </div>
                    </div>
                </div>

                <?php
//Warnings
                ////            echo Alert::widget([
                //               'block' => true,
                //               'fade' => true,
                //          ]);
                ?>

                <?php echo $content; //Pagina zelf         ?>

            </div>

            <div class="container-fluid dark topmargin">
                <div class="container">
                    <div class="row">
                        <div class="span8">
                            <h3>Onze partners</h3>
                            <div class="row-fluid">
                                <div class="span4">
                                    <a href="http://www.urkerbotter.nl/" target="_new">Stichting Urker Botter</a><br />
                                    <a href="http://www.westhaven.opurk.nl/" target="_new">Historische Westhaven Urk</a><br />
                                    <a href="http://www.urkinoorlogstijd.nl/" target="_new">Stichting Urk in Oorlogstijd</a><br />
                                    <a href="http://www.scaburk.nl" target="_new">SCAB Urk</a><br />
                                    <a href="http://www.flevomeerbibliotheek.nl/informatie/vestigingen/urk.php" target="_new">Bibliotheek Urk</a><br />
                                    <a href="http://rvu.home.xs4all.nl/svvu/index.htm" target="_new">Stichting Vrienden van Urk</a><br />
                                    <a href="http://www.urkfm.nl/" target="_new">Urk FM</a><br />
                                    <a href="http://www.ijsselacademie.nl/" target="_new">IJsselacademie</a><br />
                                </div>
                                <div class="span4">
                                    <a href="http://www.urksfruit.nl/" target="_new">Urksfruit</a><br />
                                    <a href="http://www.urk.nl/" target="_new">Gemeente Urk</a><br />
                                    <a href="http://www.buurtwerk.org" target="_new">Stichting Buurtwerk</a><br />
                                    <a href="http://botterswesthavenurk.nl/uk-213" target="_new">Botters Westhaven Urk UK 213</a>
                                    <a href="http://www.visveilingurk.nl" target="_new">Visveiling Urk</a><br />
                                    <a href="http://www.hoekstra-urk.nl" target="_new">Hoekstra Assurantie</a><br />
                                    <a href="http://www.pietbrouwer.nl" target="_new">Piet Brouwer bv</a><br />
                                    <a href="http://www.rabobank.nl/particulieren/lokalebanken/noordoostpolder-urk/" target="_new">Rabobank</a><br />
                                </div>
                                <div class="span4">
                                    <a href="http://www.van-slooten.nl/" target="_new">Autobedrijf Van Slooten</a><br />
                                    <a href="http://www.notarissteenhuis.nl/" target="_new">Notaris Steenhuis</a><br />
                                    <a href="http://www.multiscan.nl/" target="_new">Multiscan</a><br />
                                    <a href="http://www.gbu.nl/" target="_new">GBU grafisch compleet</a><br />
                                    <a href="http://www.heturkerland.nl/" target="_new">Het Urkerland</a><br />
                                    <a href="http://www.brandsfundraising.nl" target="_new">Brands Fundraising</a><br />
                                    <a href="http://www.cameranu.nl" target="_new">CameraNu.nl</a><br />
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                            <div class="row-fluid">
                                <h3>Ook meehelpen?</h3>
                                <p>Met uw steun zorgt u er samen met ons voor dat ons grote cultuurbezit, 'oenze taol', blijft bestaan!</p><p>Steun ons project en doneer een bedrag op onze bankrekening: 11.40.24.820 Rabobank Urk. Bedankt voor uw hulp!</p>
                                <a class="btn btn-primary" href="https://www.justgiving.nl/nl/charities/83-stichting-%20urker-taol" target="_new">Direct doneren</a>
                                <a class="btn btn-primary" href="<?php echo Yii::$app->basePath; ?>donateursformulier.pdf" target="_new">Donateursformulier</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span12 copyright">
                            <a href="mailto:info@documentatiecentrumurk.nl">Neem contact met ons op</a> | <a href="index.php?r=site/disclaimer">Disclaimer</a> | Ondersteund door het <a href="http://www.vsbfonds.nl/" target="_new">VSB Fonds</a> en het <a href="https://www.rabobank.nl/particulieren/lokalebanken/flevoland/cooperatiefonds/" target="_new">Cooperatiefonds Rabobank</a> | <span>Documentatiecentrumurk.nl - Versie 0.5</span><br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        //   if (!Yii::$app->user->isGuest) {
        //       NavBar::begin(['brandLabel' => 'Beheer', ]);
        echo Nav::widget([
            //    'type' => 'inverse',
            //    'brand' => 'Beheer',
            //   'brandUrl' => 'index.php',
            //   'collapse' => true,
            //   'fixed' => 'bottom',
            'items' => [
                ['label' => 'Zoeken', 'icon' => 'search white', 'url' => ['/']],
                ['label' => 'Gebruikers', 'icon' => 'user white', 'url' => ['/user']],
                ['label' => 'Afbeeldingen', 'icon' => 'picture white', 'url' => ['/image']],
                ['label' => 'Documenten', 'icon' => 'file white', 'url' => ['/document']],
                ['label' => 'Audio', 'icon' => 'headphones white', 'url' => ['/audio']],
                ['label' => 'Collecties', 'icon' => 'folder-open white', 'url' => ['/collection']],
            ],
            'options' => ['class' => 'navbar-nav'],
        ]);
        //      NavBar::end();
        //  }
        // $this->endBody();
        ?>
    </body>
</html>
<?php $this->endPage() ?>
