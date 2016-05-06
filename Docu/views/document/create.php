<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Document */

$this->title = 'Create Document';
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Lijst van Documenten', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Beheer documenten', 'icon' => 'file', 'url' => ['admin'],'icon'=>'list-alt', 'visible' => Yii::app()->user->checkAccess('admin')],
];
?>

<h1>Maak documenten aan</h1>

    <?= $this->render('_create', [
        'model' => $model,
    ]) ?>
