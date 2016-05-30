<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Gebruikers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items'=>[
	['label'=>'Acties','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Lijst van gebruikers','url'=>['index'],'icon'=>'list','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Maak gebruiker aan','url'=>['create'],'icon'=>'file','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Bewerk gebruiker','url'=>['update','id'=>$model->id],'icon'=>'pencil','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Verwijder gebruiker','url'=>'#','icon'=>'trash','linkOptions'=>['submit'=>['delete','id'=>$model->id],'confirm'=>'Weet je zeker dat je deze gebruiker wilt verwijderen?'],'visible'=>Yii::$app->user->getIdentity('admin')],
	['label'=>'Beheer gebruiker','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::$app->user->getIdentity('admin')],
]
        ]);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'roles',
        ],
    ]) ?>
</div>
