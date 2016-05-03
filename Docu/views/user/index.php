<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gebruikers';
$this->params['breadcrumbs'][] = $this->title;

// nog niet zeker correct
$this->params['menu'][] = [
        ['label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Maak gebruiker aan','url'=>['create'],'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Beheer gebruikers','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')],
];
//

?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'email:email',
            'roles',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
