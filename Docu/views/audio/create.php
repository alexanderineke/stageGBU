<?php

use yii\helpers\Html;

$this->title = 'Aanmaken';
$this->params['breadcrumbs'][] = ['label' => 'Documneten', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
        ['label' => 'Lijst van documenten', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::app()->user->checkAccess('moderator')],
        ['label' => 'Beheer documenten', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::app()->user->checkAccess('admin')],
    ],
]);
?>

<h1>Maak audio bestand aan</h1>

<?=
$this->render('_create', [
    'model' => $model,
])
?>
