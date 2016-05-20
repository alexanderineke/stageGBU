<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Afbeeldingen';
$this->params['breadcrumbs'][] = $this->title;
        
echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak afbeeldingen aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],
        ['label' => 'Beheer afbeeldingen', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
   ]]);
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
       // 'type' => 'striped bordered',
        'dataProvider' => $model->search(),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['label' => 'images', 'format' => 'html', //'htmlOptions' => [
                   // 'style' => 'width: 100px; text-align: center;',
               /*];*/  'header' => 'Voorbeeld'/*, 'value' => 'CHtml::image("uploads/afbeeldingen/".$data->images[0]->location."/thumb/".$data->images[0]->file.$data->images[0]->format)'*/],
            ['label' => 'title', 'header' => 'Titel', 'format' => 'html',/* 'value' => 'CHtml::link($data->title, Yii::app()->createUrl("image/view",["id"=>$data->id)])'*/],
            ['class' => 'yii\grid\ActionColumn'],
        ],
      //  'enableHistory' => true,
        'pager' => [
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
        ],
    ]);
    ?>

</div>
