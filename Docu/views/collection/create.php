<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Collection */

$this->title = 'Maak een collectie';
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
   	['label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Lijst van collecties','url'=>['index'],'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Beheer collecties','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')],
];
?>
<div class="collection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
