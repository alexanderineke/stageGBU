<?php

use yii\helpers\Html;

$this->title = 'Beheer Gebruikers';
$this->params['breadcrumbs'][] = ['label' => 'User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Lijst van gebruikers', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Maak gebruiker aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::app()->user->checkAccess('moderator')],
 ];

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

<?=
GridView::widget([
    'id' => 'user-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => [
        'id',
        'username',
        'email',
        ['class' => 'btn btn-primary'],
    ],
])
?>