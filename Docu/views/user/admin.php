<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;

$this->title = 'Beheer';
$this->params['breadcrumbs'][] = ['label' => 'Gebruikers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van gebruikers', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak gebruiker aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::$app->user->getIdentity('moderator')],
]]);
?>

<h1><?= Html::encode($this->title) ?></h1>


<?php // Html::a('Geavanceerd zoeken', '#', ['class' => 'btn btn-primary']) ?>
<div class="search-form" style="display: none">
    <?php $this->render('_search', ['model' => $model], true); ?>
</div>

<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'username',
        'email',
        ['class' => 'yii\grid\ActionColumn'],
    ],
])
?>