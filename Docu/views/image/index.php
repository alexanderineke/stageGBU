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
    ],
]);

function objectToTagString($tags) {
    $string = [];
    foreach ($tags as $tag) {
        $string[] = $tag["name"];
    }
    return implode(", ", $string);
}

function fileLocation($id, $title) {
    return Yii::getAlias($title, ['audio/view', 'id' => $id]);
}
?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

    <p>
        <?= Html::a('Create Image', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'id' => 'image-grid',
        // 'type' => 'striped bordered',
        'dataProvider' => $model->search(),
        'columns' => [
        //    ['header' => 'Voorbeeld', 'value'=>'CHtml::image("uploads/afbeeldingen/".$data->images[0]->location."/thumb/".$data->images[0]->file.$data->images[0]->format)'], 
            ['header' => 'Naam imagebestand', 'value' => function($data) {
                    $file = fileLocation($data->id, $data->title);
                    return Html::a(Html::encode($file), 'index.php?r=image%2Fview&id=' . $data->id);
                }, 'format' => 'raw'],
        ],
        'pager' => [
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
        ],
    ]);
    ?>
</div>
