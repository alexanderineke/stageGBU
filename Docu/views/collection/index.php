<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Collecties';
$this->params['breadcrumbs'][] = $this->title;
        
$this->params['menu'][] = [
        ['label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')],
	['label'=>'Maak collectie aan','url'=>['create'],'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')],
        ['label'=>'Beheer collecties','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')],
];
?>
<div class="collection-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Collection', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'itemView' => '_view',
    ]);
    ?>

</div>
