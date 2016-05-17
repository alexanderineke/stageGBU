<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;

$this->title = 'Beheer Collecties';
$this->params['breadcrumbs'][] = ['label' => 'Collecion', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
    ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Maak collectie aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Lijst van collecties', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Uitgelichte collecties', 'icon' => 'eye-open', 'url' => ['view&id=17'], 'visible' => Yii::$app->user->getIdentity('moderator')],
]]);
/*
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
");*/
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= Html::a('Geavanceerd zoeken', '#', ['class' => 'btn btn-primary']) ?>
<div class="search-form" style="display: none">
    <?php $this->render('_search', ['model' => $model], true); ?>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'title',
        'created_on',
        'modified_on',
       ['class' => 'yii\grid\ActionColumn'],
    ],
])
?>