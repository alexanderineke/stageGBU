<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gebruikers';
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak gebruiker aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Beheer gebruikers', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ]
]);

function fileLocation($id, $title) {
    return Yii::getAlias($title, ['user/view', 'id' => $id]);
}
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['header' => 'Gebruikersnaam',
                'format' => 'raw',
                'value' => function($data) {
                    $file = fileLocation($data->id, $data->username);
                    return Html::a(Html::encode($file), 'index.php?r=user%2Fview&id=' . $data->id);
                },
            ],
            'email',
            'roles',
        ],
    ]);
    ?>


</div>
