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

//echo Button::widget([
//    'options' => ['id' => 'selector-button', 'data-toggle' => 'false'],
//    'label' => 'Maak een selectie',
//]);
//$gridDataProvider = new ArrayDataProvider([
//  ['id' => 0, 'title' => 'Geen selectie'],]);
//echo GridView::widget([
//    'dataProvider' => $imageSearch,
//    'columns' => [
//        'id',
//        'state'
//    ],
//]);

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
                //  'itemsCssClass' => 'og-grid',
                //   'itemsTagName' => 'ul',
                //  'ajaxUpdate' => false,
                //  'enableHistory' => true,
        ]),
            //   'active' => true
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
    $this->registerCssFile(Yii::getAlias('@web/themes/dcu') . '/assets/css/swipebox.min.css');
    $this->registerJs(Yii::getAlias('@web/themes/dcu') . '/assets/js/jquery.swipebox.min.js', View::POS_END);
    // $cs = Yii::$app->getClientScript();
    if ($documentSearch->getTotalCount() < 3) {
        //     $cs->registerScript('document-gallery', '$(".documentList").find("a.swipebox").swipebox();');
    } else {
        $this->registerJs(Yii::getAlias('@web/themes/dcu') . '/assets/js/jquery.justifiedGallery.min.js', View::POS_END);
        //     $cs->registerScript('document-gallery', '$(".documentList").justifiedGallery({rowHeight : 240, margins: 2, cssAnimation: true }).on("jg.complete", function () { $(this).find("a.swipebox").swipebox(); });');
    }

    $results[] = [
        'label' => '<i class="icon-file icon-white"></i>  Documenten <span class="badge">' . $documentSearch->getTotalCount() . '</span>',
        'id' => 'document-grid-tab',
        'content' => ListView::widget([
            'dataProvider' => $documentSearch,
            'itemView' => '_documents',
                //         'itemsCssClass' => 'documentList',
                //         'itemsTagName' => 'div',
                //        'ajaxUpdate' => false,
                //       'enableHistory' => true,
        ]),
        'active' => $documentSearch->getTotalCount() == 0
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-file icon-white"></i>  Documenten <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
    ];
}

if (($audioSearch->getTotalCount() != 0)) { // && !Yii::$app->request->isAjaxRequest) || Yii::$app->request->isAjaxRequest) {
    $results[] = [
        'label' => '<i class="icon-headphones icon-white"></i> Audio <span class="badge">' . $audioSearch->getTotalCount() . '</span>',
        'id' => 'audio-grid-tab',
        'content' => GridView::widget([
            'dataProvider' => $audioSearch,
            //    'filterModel' => $audioModel,
            'columns' => [
                //    'title',
                'title',
                'created_on',
            //    'created_on'
            //  ['header' => 'Naam document', 'value' => /* Html::a($audioModel->title, Url::to("/docu/web/index.php?r=audio%2Fview&id=" . $audioModel->id)), 'format' => 'raw', 'filter' => Html::activeTextInput($audioModel, 'title', ['placeholder' => 'Zoek op titel..']) */ ],
            //  ['header' => 'Steekwoorden', 'value' => 'objectToTagStringAudio($data->id)', 'filter' => Html::activeTextInput($audioModel, 'tag_search', ['placeholder' => 'Zoek op steekwoord..'])], //Omdat een result meerdere tags kan hebben moeten we deze verwerken.
            //   ['header' => 'Toegevoegd op', 'value' => '($data->created_on !== "0000-00-00 00:00:00" ? strftime("%e %B %Y", strtotime($data->created_on)) : "Datum niet beschikbaar")', 'filter' => ''],
            ],
            //   'enableHistory' => true,
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

