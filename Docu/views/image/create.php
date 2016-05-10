<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Image */

$this->title = 'Maak Afbeelding Aan';
$this->params['breadcrumbs'][] = ['label' => 'Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        [['label' => 'Acties', 'visible'] => Yii::$app->user->getIdentity('moderator')],
        [['label' => 'Lijst van afbeeldingen', 'url'] => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        [['label' => 'Beheer afbeeldingen', 'url'] => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>
<div class="image-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
