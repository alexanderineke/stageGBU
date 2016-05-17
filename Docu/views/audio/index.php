<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;

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
    return Yii::getAlias($title, ['audio/view', 'id' => $id], $options = []);
}
?>

<h1><?= Html::encode($this->title); ?></h1>

<?=
GridView::widget([
    'dataProvider' => $model->search(),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        ['header' => 'Naam audiobestand', 'value' => function($data) {
                return fileLocation($data->id, $data->title);
            }],
        ['header' => 'Tags', 'value' => function($data) {
                return objectToTagString($data->tags);
            }],
        ['header' => 'Jaar'],
        ['class' => 'yii\grid\ActionColumn'],
    ],
    'pager' => [
        'prevPageLabel' => '&laquo;',
        'nextPageLabel' => '&raquo;',
    ],
]);
?>