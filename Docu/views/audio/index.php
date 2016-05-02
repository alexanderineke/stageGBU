<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Audios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audio-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Audio', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'title',
            'description:ntext',
            'year',
            // 'owner',
            // 'created_on',
            // 'modified_on',
            // 'published',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
