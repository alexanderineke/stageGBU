<?php

use yii\helpers\Html;

$this->title = 'Bewerk';
$this->params['breadcrumbs'][] = ['label' => 'Audio', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van audio bestanden', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak audio bestanden aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],
        ['label' => 'Bekijk audio bestand', 'url' => ['view', 'id'=>$model->id], 'icon' => 'eye-open', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Beheer audio bestanden', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>

<h1>Bewerk audio bestanden <?php echo $model->$id; ?></h1>

    <?= $this->render('_update', [
        'model' => $model,
    ]) ?>
