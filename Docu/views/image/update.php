<?php

use yii\helpers\Html;
use yii\widgets\Menu;
/* @var $this yii\web\View */
/* @var $model app\models\Image */

$this->title = 'Bewerk afbeelding: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van afbeeldingen', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak afbeeldingen aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],
        ['label' => 'Bekijk afbeelding', 'url' => ['view','id'=>$model->id], 'icon' => 'eye-open', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Beheer afbeeldingen', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>
<div class="image-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
