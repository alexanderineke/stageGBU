<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\Pagination;

$this->title = 'Audio';
$this->params['breadcrumbs'][] = $this->title;


echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak audio bestanden aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],
        ['label' => 'Beheer audio bestanden', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
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
    return Html::a($title, ['audio/view', 'id' => $id]);
}
?>

<h1>Audio</h1>

<?=
GridView::widget([
    'id' => 'audio-grid',
    'type' => 'striped bordered',
    'dataProvider' => $model->search(),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        ['name'=>'title', 'header'=>'Naam audiobestand','value'=>'fileLocation($data->id, $data->title)', 'type'=>'raw'],
        ['name'=>'tag_search', 'header'=>'Tags', 'value'=>'objectToTagString($data->tags)'],
        ['name'=>'year', 'header'=>'Jaar'],
        ['class' => 'yii\grid\ActionColumn'],
    ],
    'enableHistory'=>true,
    'pager'=> [
        'prevPageLabel' => '&laquo;',
        'nextPageLabel' => '&raquo;',
    ],
    ]);
?>