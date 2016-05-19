<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documents';
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
    ['label' => 'Maak Documenten aan', 'icon' => 'file', 'url' => ['create'], 'visible' => Yii::$app->user->getIdentity('user')],
    ['label' => 'Beheer documenten', 'icon' => 'file', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);

function objectToTagString($tags) {
    $string = [];
    foreach ($tags as $tag) {
        $string[] = $tag["name"];
    } return implode(", ", $string);
}

function fileLocation($id, $title) {
    return Html::a($title, ['document/view', 'id' => $id], $options = []);
}
?>

<h1><?= Html::encode($this->title) ?></h1>

<?=
GridView::widget([
    'dataProvider' => $model->search(),
    'columns' => [
        ['header' => 'Naam document', 'value' => function($data) {
                $file = fileLocation($data->id, $data->title);
                return Html::a(($file), 'index.php?r=document%2Fview&id=' . $data->id);
            }, 'format' => 'raw'],
        ['header' => 'Steekwoorden', 'value' => function($data) {
                return objectToTagString($data->tags);
            }],
        ['header' => 'Jaar', 'value' => 'year'],
    ],
    'pager' => [
        'prevPageLabel' => '&laquo;',
        'nextPageLabel' => '&raquo;',
    ],
])
?>