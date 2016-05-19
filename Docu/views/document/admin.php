<?php

use yii\helpers\Html;
use yii\widgets\Menu;
use yii\grid\GridView;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Documenten', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van Documenten', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak Documenten aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::$app->user->getIdentity('user')],
    ],
]);

/*
  Yii::$app->clientScript->registerScript('search', "
  $('.search-button').click(function(){
  $('.search-form').toggle();
  return false;
  });
  $('.search-form form').submit(function(){
  $.fn.yiiGridView.update('document-grid', {
  data: $(this).serialize()
  });
  return false;
  });
  ");
 */
?>

<h1>Beheer documenten</h1>

<p>
    U kan optioneel een vergelijks symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

<?= Html::a('Geavanceerd zoeken', '#', ['class' => 'btn btn-default']); ?>
<div class="search-form" style="display:none">
    <?php
    $this->render('_search', [
        'model' => $model, true
    ]);
    ?>
</div>

<?=
GridView::widget([
    'id' => 'document-grid',
    'dataProvider' => $model->search(),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'title',
        //    'filter' => Html::activeInput('text', $model, 'title', ['placeholder' => 'Zoek op titel...'])
        'created_on',
        'modified_on',
        ['class' => 'yii\grid\ActionColumn'],
    //   ['class' => 'btn btn-default'],
    ],
])
?>