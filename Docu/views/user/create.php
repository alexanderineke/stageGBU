<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Maak Gebruiker aan';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// nog niet zeker correct
$this->params['menu'][] = [
   	['label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Lijst van gebruikers','url'=>['index'],'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Beheer gebruikers','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')],
];

?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
