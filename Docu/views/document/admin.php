<?php

use yii\helpers\Html;
use yii\widgets\Menu;
use yii\grid\GridView;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Documenten', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van Documenten', 'icon' => 'list', 'url' => ['index'], 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak Documenten aan', 'icon' => 'file', 'url' => ['create'], 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('user')],
    ],
]);
?>

<h1>Beheer documenten</h1>

<p>
    U kan optioneel een vergelijks symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

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
        'created_on',
        'modified_on',
        ['class' => 'yii\grid\ActionColumn'],
    ],
])
?>