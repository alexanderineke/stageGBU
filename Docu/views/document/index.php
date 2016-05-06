<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documents';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menu'][] = [
    ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
    ['label' => 'Maak Documenten aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::app()->user->checkAccess('user')],
    ['label' => 'Beheer documenten', 'icon' => 'file', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::app()->user->checkAccess('admin')],
];

function objectToTagString($tags) {
    $string = [];
    foreach ($tags as $tag) {
        $string[] = $tag["name"];
    } return implode(", ", $string);
}

function fileLocation($id, $title) {
    return Html::a($title, ['document/view', 'id' => $id]);
}
?>

<h1>Documenten</h1>

<?=
GridView::widget([
    'id' => 'document-grid',
    'type' => 'striped bordered',
    'dataProvider' => $model->search(),
    'columns' => [
        ['name' => 'title', 'header' => 'Naam document', 'value' => 'fileLocation($data->id, $data->title)', 'type' => 'raw'],
        ['name' => 'tag_search', 'header' => 'Tags', 'value' => 'objectToTagString($data->tags)'],
        ['name' => 'year', 'header' => 'Jaar'],
    ],
    'enableHistory' => true,
    'pager' => [
        'prevPageLabel' => '&laquo;',
        'nextPageLabel' => '&raquo;',
    ],
])
?>