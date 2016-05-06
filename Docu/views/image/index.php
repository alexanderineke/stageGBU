<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Afbeeldingen';
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
        ['label' => 'Maak afbeeldingen aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::app()->user->checkAccess('user')],
        ['label' => 'Beheer afbeeldingen', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::app()->user->checkAccess('admin')],
    ],
]);
?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Image', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'id' => 'image-grid',
        'type' => 'striped bordered',
        'dataProvider' => $model->search(),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['name' => 'images', 'type' => 'html', 'htmlOptions' => [
                    'style' => 'width: 100px; text-align: center;',
                ], 'header' => 'Voorbeeld', 'value' => 'CHtml::image("uploads/afbeeldingen/".$data->images[0]->location."/thumb/".$data->images[0]->file.$data->images[0]->format)'],
            ['name' => 'title', 'header' => 'Titel', 'type' => 'html', 'value' => 'CHtml::link($data->title, Yii::app()->createUrl("image/view",["id"=>$data->id)])'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'enableHistory' => true,
        'pager' => [
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
        ],
    ]);
    ?>

</div>
