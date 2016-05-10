<?php

use yii\helpers\Html;

$this->title = 'Beheer Collecties';
$this->params['breadcrumbs'][] = ['label' => 'Collecion', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::$app->user->getIndentity('moderator')],
    ['label' => 'Maak collectie aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::$app->user->getIndentity('moderator')],
    ['label' => 'Lijst van collecties', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::$app->user->getIndentity('moderator')],
    ['label' => 'Uitgelichte collecties', 'icon' => 'eye-open', 'url' => ['view&id=17'], 'visible' => Yii::$app->user->getIndentity('moderator')],
];

Yii::$app->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('collection-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>
    U kan optioneel een vergelijk symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

<?= Html::a('Geavanceerd zoeken', '#', ['class' => 'btn btn-primary']) ?>
<div class="search-form" style="display: none">
    <?php $this->render('_search', ['model' => $model], true); ?>
</div>

<?= GridView::widget([
    'id' => 'collection-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => [
        'title',
        'created_on',
        'modified_on',
        ['class' => 'btn btn-primary'],
    ],
])
?>