<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Audiobestanden', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => !Yii::$app->user->isGuest], //Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Lijst van audio', 'icon' => 'list', 'url' => ['index'], 'visible' => !Yii::$app->user->isGuest], //Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Maak audio bestanden aan', 'icon' => 'file', 'url' => ['create'], 'visible' => !Yii::$app->user->isGuest], //Yii::$app->user->getIdentity('user')],
];
?>

<h1>Beheer audio bestanden</h1>

<p>
    U kan optioneel een vergelijk symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

<div class="search-form" style="display: none">
    <?php Yii::$app->controller->renderPartial('_search', ['model' => $model, true]) ?>
</div>

<?=
GridView::widget([
    'id' => 'audio-grid',
    'dataProvider' => $model->search(),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'title',
        'created_on',
        'modified_on',
        ['class' => ActionColumn::className()],
    ],
])
?>