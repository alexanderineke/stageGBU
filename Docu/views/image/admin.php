<?php

use yii\helpers\Html;
use yii\widgets\Menu;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Afbeelding', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        [['label' => 'Acties', 'visible'] => Yii::$app->user->getIdentity('moderator')],
        [['label' => 'Lijst van afbeeldingen', 'url'] => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        [['label' => 'Maak afbeeldingen aan', 'url'] => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],
]]);


//Hier moet registerscript komen
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
    'id' => 'image-grid',
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