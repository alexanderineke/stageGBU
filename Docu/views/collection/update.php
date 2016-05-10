<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Collection */

$this->title = 'Bewerk collectie: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Bewerk';

$this->params['menu'][] = [
   	['label'=>'Acties','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Maak collectie aan','url'=>['create'],'icon'=>'file','visible'=>Yii::$app->user->getIdentity('moderator')],
        ['label'=>'Lijst van collecties','url'=>['index'],'icon'=>'list','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Beheer collecties','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::$app->user->getIdentity('admin')],
];


?>
<div class="collection-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
