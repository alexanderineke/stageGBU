<?php

$this->widget('bootstrap.widgets.TbButton', [
    'htmlOptions' => ['id' => 'selector-button', 'data-toggle' => 'false'],
    'label' => 'Maak een selectie',
    'type' => 'primary',
    'icon' => 'inbox white'
]);

$gridDataProvider = new CArrayDataProvider([
    ['id' => 0, 'type' => '', 'title' => 'Geen selectie'],
        ]);

$this->widget('bootstrap.widgets.TbGridView', [
    'type' => 'striped bordered condensed',
    'dataProvider' => $gridDataProvider,
    'template' => '{items}',
    'columns' => [
        ['name' => 'type', 'type' => 'raw', 'header' => 'Type', 'htmlOptions' => ['width' => '15%']],
        ['name' => 'title', 'header' => 'Naam'],
        ['name' => 'action', 'header' => 'Verwijder']
    ],
    'htmlOptions' => [
        'style' => 'display:none;',
        'id' => 'selection-widget'
    ]
]);

function objectToTagString($tags) { //Vertaalt de verschillende tags naar 1 string met alle tags.
    return Document::model()->findByPk($tags)->tagshelper;
}

function objectToTagStringAudio($tags) { //Vertaalt de verschillende tags naar 1 string met alle tags.
    return Audio::model()->findByPk($tags)->tagshelper;
}

if ($imageSearch->totalItemCount != 0) {
    Yii::$app->clientScript->registerCssFile(Yii::$app->theme->baseUrl . '/assets/css/grid/grid.css');
    Yii::$app->clientScript->registerCssFile(Yii::$app->theme->baseUrl . '/assets/css/grid/component.css');
    Yii::$app->clientScript->registerCssFile(Yii::$app->theme->baseUrl . '/assets/css/grid/default.css');
    Yii::$app->clientScript->registerScriptFile(Yii::$app->theme->baseUrl . '/assets/js/grid.js', CClientScript::POS_END);
    $cs = Yii::$app->getClientScript();
    $cs->registerScript('image-grid', 'Grid.init();');

    $results[] = [
        'label' => '<i class="icon-picture icon-white"></i> Afbeeldingen <span class="badge">' . $imageSearch->totalItemCount . '</span>',
        'id' => 'image-list-tab',
        'content' => $this->widget('bootstrap.widgets.TbListView', [
            'dataProvider' => $imageSearch,
            'itemView' => '_images',
            'itemsCssClass' => 'og-grid',
            'itemsTagName' => 'ul',
            'ajaxUpdate' => false,
            'enableHistory' => true,
                ], true),
        'active' => true
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-picture icon-white"></i> Afbeeldingen <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
        'active' => $documentSearch->totalItemCount == 0 && $audioSearch->totalItemCount == 0
    ];
}

if (($documentSearch->totalItemCount != 0 && !Yii::$app->request->isAjaxRequest) || Yii::$app->request->isAjaxRequest) {
    Yii::$app->clientScript->registerCssFile(Yii::$app->theme->baseUrl . '/assets/css/justifiedGallery.min.css');
    Yii::$app->clientScript->registerCssFile(Yii::$app->theme->baseUrl . '/assets/css/swipebox.min.css');
    Yii::$app->clientScript->registerScriptFile(Yii::$app->theme->baseUrl . '/assets/js/jquery.swipebox.min.js', CClientScript::POS_END);
    $cs = Yii::$app->getClientScript();
    if ($documentSearch->getTotalItemCount() < 3) {
        $cs->registerScript('document-gallery', '$(".documentList").find("a.swipebox").swipebox();');
    } else {
        Yii::$app->clientScript->registerScriptFile(Yii::$app->theme->baseUrl . '/assets/js/jquery.justifiedGallery.min.js', CClientScript::POS_END);
        $cs->registerScript('document-gallery', '$(".documentList").justifiedGallery({rowHeight : 240, margins: 2, cssAnimation: true }).on("jg.complete", function () { $(this).find("a.swipebox").swipebox(); });');
    }

    $results[] = [
        'label' => '<i class="icon-file icon-white"></i>  Documenten <span class="badge">' . $documentSearch->totalItemCount . '</span>',
        'id' => 'document-grid-tab',
        'content' => $this->widget('bootstrap.widgets.TbListView', [
            'dataProvider' => $documentSearch,
            'itemView' => '_documents',
            'itemsCssClass' => 'documentList',
            'itemsTagName' => 'div',
            'ajaxUpdate' => false,
            'enableHistory' => true,
                ], true),
        // 'content' => $this->widget('bootstrap.widgets.TbGridView', array(
        //     'type'=>'striped bordered',
        //     'dataProvider'=>$documentSearch,
        //     'filter'=>$documentModel,
        //     'columns'=>array(
        //         array('name'=>'title', 'header'=>'Naam document', 'value'=>'CHtml::link($data->title, Yii::$app->createUrl("document/view",array("id"=>$data->id)))', 'type'=>'raw', 'filter'=>CHtml::activeTextField($documentModel, 'title', array('placeholder'=>'Zoek op titel..'))),
        //         array('name'=>'tag_search', 'header'=>'Steekwoorden', 'value'=>'objectToTagString($data->id)', 'filter'=>CHtml::activeTextField($documentModel, 'tag_search', array('placeholder'=>'Zoek op steekwoord..'))), //Omdat een result meerdere tags kan hebben moeten we deze verwerken.
        //         array('name'=>'created_on', 'header'=>'Toegevoegd op', 'value'=>'($data->created_on !== "0000-00-00 00:00:00" ? strftime("%e %B %Y", strtotime($data->created_on)) : "Datum niet beschikbaar")', 'filter'=>''),
        //     ), 
        //     'enableHistory'=>true,
        //     'pager' => array('class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'),                
        // ), true),
        'active' => $imageSearch->totalItemCount == 0
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-file icon-white"></i>  Documenten <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
    ];
}

if (($audioSearch->totalItemCount != 0 && !Yii::$app->request->isAjaxRequest) || Yii::$app->request->isAjaxRequest) {
    $results[] = [
        'label' => '<i class="icon-headphones icon-white"></i> Audio <span class="badge">' . $audioSearch->totalItemCount . '</span>',
        'id' => 'audio-grid-tab',
        'content' => $this->widget('bootstrap.widgets.TbGridView', [
            'type' => 'striped bordered',
            'dataProvider' => $audioSearch,
            'filter' => $audioModel,
            'columns' => [
                ['name' => 'title', 'header' => 'Naam document', 'value' => 'CHtml::link($data->title, Yii::$app->createUrl("audio/view",array("id"=>$data->id)))', 'type' => 'raw', 'filter' => CHtml::activeTextField($audioModel, 'title', ['placeholder' => 'Zoek op titel..'])],
                ['name' => 'tag_search', 'header' => 'Steekwoorden', 'value' => 'objectToTagStringAudio($data->id)', 'filter' => CHtml::activeTextField($audioModel, 'tag_search', ['placeholder' => 'Zoek op steekwoord..'])], //Omdat een result meerdere tags kan hebben moeten we deze verwerken.
                ['name' => 'created_on', 'header' => 'Toegevoegd op', 'value' => '($data->created_on !== "0000-00-00 00:00:00" ? strftime("%e %B %Y", strtotime($data->created_on)) : "Datum niet beschikbaar")', 'filter' => ''],
            ],
            'enableHistory' => true,
            'pager' => ['class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'],
                ], true),
        'active' => $documentSearch->totalItemCount == 0 && $imageSearch->totalItemCount == 0
    ];
} else {
    $results[] = [
        'label' => '<i class="icon-headphones icon-white"></i> Audio <span class="badge">0</span>',
        'content' => '<span class=\'empty\'>Geen resultaten gevonden.</span>',
    ];
}

$this->widget('bootstrap.widgets.TbTabs', [
    'type' => 'tabs', // 'tabs' or 'pills'
    'encodeLabel' => false,
    'tabs' => $results,
]);
?>
