<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Bewerk gebruiker: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$this->menu=[
        ['label'=>'Acties','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Lijst van gebruikers','url'=>['index'],'icon'=>'list','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Maak gebruiker aan','url'=>['create'],'icon'=>'file','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Bekijk gebruiker','url'=>['view','id'=>$model->id],'icon'=>'eye-open','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Beheer gebruiker','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::$app->user->getIdentity('admin')],
];
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
