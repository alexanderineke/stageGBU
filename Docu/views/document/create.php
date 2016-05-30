<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Document */

$this->title = 'Aanmaken';
$this->params['breadcrumbs'][] = ['label' => 'Documenten', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Lijst van Documenten', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Beheer documenten', 'icon' => 'file', 'url' => ['admin'],'icon'=>'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
];
?>

<h1>Maak documenten aan</h1>

    <?= $this->render('_create', [
        'model' => $model,
    ]) ?>
