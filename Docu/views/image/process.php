<?php

use yii\helpers\Html;
use yii\widgets\Menu;

$this->title = 'Verwerk document ' . $file['file'];
$this->params['breadcrumbs'][] = ['label' => 'Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van afbeeldingen', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Beheer afbeeldingen', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>

<h1><?= Html::encode($this->title) ?></h1>

<?=
$this->render('_process', [
    'model' => $model,
    'file' => $file,
  //  'collection_list' => $collection_list,
])
?>