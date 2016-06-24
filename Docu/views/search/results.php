<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\bootstrap\Button;
use app\models\Document;
use app\models\Audio;
use yii\helpers\Url;
use app\assets\AppAsset;
use yii\web\View;

AppAsset::register($this);

function objectToTagString($tags) { //Vertaalt de verschillende tags naar 1 string met alle tags.
    return Document::model()->findOne($tags)->tagshelper;
}

function objectToTagStringAudio($tags) { //Vertaalt de verschillende tags naar 1 string met alle tags.
    return (new Audio)->findOne($tags)->tagshelper;
}

if ($imageSearch->getTotalCount() != 0) {
    $this->registerCssFile(Yii::getAlias('@web/themes/dcu') . '/assets/css/grid/grid.css');
    $this->registerCssFile(Yii::getAlias('@web/themes/dcu') . '/assets/css/grid/component.css');
    $this->registerCssFile(Yii::getAlias('@web/themes/dcu') . '/assets/css/grid/default.css');
    $this->registerJs(Yii::getAlias('@web/themes/dcu') . '/assets/js/grid.js', View::POS_END);
    //  $cs = Yii::$app->getClientScript();
    //  $cs->registerScript('image-grid', 'Grid.init();');

    $results[] = [
        'label' => '<i class="icon-picture icon-white"></i> Afbeeldingen <span class="badge">' . $imageSearch->getTotalCount() . '</span>',
        'id' => 'image-list-tab',
        'content' => ListView::widget([
            'dataProvider' => $imageSearch,
            'itemView' => '_images',
        ]),
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-picture icon-white"></i> Afbeeldingen <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
        'active' => $documentSearch->getTotalCount() == 0 && $audioSearch->getTotalCount() == 0
    ];
}

if (($documentSearch->getTotalCount() != 0)) { //&& !Yii::$app->request->isAjaxRequest) || Yii::$app->request->isAjaxRequest) {
    $this->registerCssFile(Yii::getAlias('@web/themes/dcu') . '/assets/css/justifiedGallery.min.css');
    $this->registerCssFile(Yii::$app->basePath . DIRECTORY_SEPARATOR .  'web' . DIRECTORY_SEPARATOR . 'themes' . 'dcu' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR .'css' . DIRECTORY_SEPARATOR . 'justifiedGallery.min.css');
    $this->registerCssFile(Yii::getAlias('@web/themes/dcu') . '/assets/css/swipebox.min.css');
    $this->registerJs(Yii::getAlias('@web/themes/dcu') . '/assets/js/jquery.swipebox.min.js', View::POS_END);
    // $cs = Yii::$app->getClientScript();
    if ($documentSearch->getTotalCount() < 3) {
        //     $this->registerJs('document-gallery', '$(".documentList").find("a.swipebox").swipebox();');
    } else {
        $this->registerJs(Yii::getAlias('@web/themes/dcu') . '/assets/js/jquery.justifiedGallery.min.js', View::POS_END);
     //        $this->registerJs('document-gallery', '$(".documentList").justifiedGallery({rowHeight : 240, margins: 2, cssAnimation: true }).on("jg.complete", function () { $(this).find("a.swipebox").swipebox(); });');
    }

    $results[] = [
        'label' => '<i class="icon-file icon-white"></i>  Documenten <span class="badge">' . $documentSearch->getTotalCount() . '</span>',
        'id' => 'document-grid-tab',
        'content' => ListView::widget([
            'dataProvider' => $documentSearch,
            'itemView' => '_documents',
        ]),
        'active' => $documentSearch->getTotalCount() == 0
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-file icon-white"></i>  Documenten <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
    ];
}

if (($audioSearch->getTotalCount() != 0)) { 
    $results[] = [
        'label' => '<i class="icon-headphones icon-white"></i> Audio <span class="badge">' . $audioSearch->getTotalCount() . '</span>',
        'id' => 'audio-grid-tab',
        'content' => GridView::widget([
            'dataProvider' => $audioSearch,
            'columns' => [
                'title',
                'created_on',
            ],
            'pager' => [
                'prevPageLabel' => '&laquo;',
                'nextPageLabel' => '&raquo;',
    ]]),
        'active' => $documentSearch->getTotalCount() == 0 && $imageSearch->getTotalCount() == 0
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-headphones icon-white"></i> Audio <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
    ];
}
echo Tabs::widget([
    'items' => $results,
    'encodeLabels' => false,
]);

