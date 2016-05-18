<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Audio', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Lijst van audio', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Maak audio bestanden aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::$app->user->getIdentity('user')],
];
/*
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('audio-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
 */
?>

<h1>Beheer audio bestanden</h1>

<p>
    U kan optioneel een vergelijk symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

<?= Html::a('Geavanceerd zoeken', '#', ['class' => 'btn btn-primary']) ?>
<div class="search-form" style="display: none">
    <?php $this->render('_search', ['model' => $model], true); ?>
</div>

<?= GridView::widget([
    'id' => 'audio-grid',
    'dataProvider' => $model->search(),
    'columns' => [
        ['label' => 'Titel', 'value' => 'title',
         'filter'=> Html::activeInput('text', $model, 'title', ['placeholder'=>'Zoek op titel...']) 
        ],
        ['label' => 'Aanmaakdatum', 'value' => 'created_on',],
        ['label' => 'Laatste wijzigingsdatum', 'value' => 'modified_on',],
        ['class' => 'yii\grid\ActionColumn'],
       // ['class' => 'btn btn-default'],
    ],
])
?>