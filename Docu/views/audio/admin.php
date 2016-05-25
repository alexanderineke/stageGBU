<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Audiobestanden', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => !Yii::$app->user->isGuest], //Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Lijst van audio', 'icon' => 'list', 'url' => ['index'], 'visible' => !Yii::$app->user->isGuest], //Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Maak audio bestanden aan', 'icon' => 'file', 'url' => ['create'], 'visible' => !Yii::$app->user->isGuest], //Yii::$app->user->getIdentity('user')],
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

<?= Html::a('Geavanceerd zoeken', '#', ['class' => 'btn btn-default']) ?>
<div class="search-form" style="display: none">
    <?php Yii::$app->controller->renderPartial('_search', ['model' => $model, true]) ?>
</div>

<?=
GridView::widget([
    'id' => 'audio-grid',
    'dataProvider' => $model->search(),
   // 'filterModel' => $model,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'title',
        //    return Html::activeInput('text', $model, 'title', ['placeholder' => 'Zoek op titel...']);
        'created_on',
        'modified_on',
        ['class' => 'yii\grid\ActionColumn'],
    // ['class' => 'btn btn-default'],
    ],
])
?>