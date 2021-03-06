<?php

use yii\helpers\Html;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $model app\models\Document */

$this->title = 'Bewerk';
$this->params['breadcrumbs'][] = ['label' => 'Documenten', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van documenten', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak audio documenten aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],
        ['label' => 'Bekijk document', 'url' => ['view', 'id'=>$model->id], 'icon' => 'eye-open', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Beheer document', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>

<h1>Bewerk document <?= $model->id; ?></h1>

    <?= $this->render('_update', [
        'model' => $model,
    ]) ?>
