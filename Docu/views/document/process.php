<?php
use yii\helpers\Html;

$this->title = 'Verwerken';
$this->params['breadcrumbs'][] = ['label' => 'Documenten', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Meerdere aanmaken', 'url' => []];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Lijst van Documenten', 'icon' => 'list', 'url' => ['index'], 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Beheer documenten', 'icon' => 'file', 'url' => ['admin'],'icon'=>'list-alt', 'visible' => Yii::app()->user->checkAccess('admin')],
];
?>

<h1>Verwerk document <?= $file['file']; ?></h1>

    <?= $this->render('_process', [
        'model' => $model,
        'file' => $file,
        'collection_list' => $collection_list,
    ]) ?>