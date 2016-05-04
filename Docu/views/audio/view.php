<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Audio */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Audioen', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::app()->user->checkAccess('moderator')],
        ['label' => 'Lijst van audio bestanden', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::app()->user->checkAccess('moderator')],
        ['label' => 'Maak audio bestanden aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::app()->user->checkAccess('user')],
        ['label' => 'Bewerk audio bestanden', 'url' => ['update', 'id' => $model->id], 'icon' => 'pencil', 'visible' => Yii::app()->user->checkAccess('moderator')],
        ['label' => 'Verwijder audio bestanden', 'url' => '#', 'icon' => 'trash', 'linkOptions' => ['submit' => ['delete', 'id' => $model->id]], 'confirm' => 'Weet je zeker dat je dit audio bestand wilt verwijderen?'],
        ['label' => 'Beheer audio bestanden', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::app()->user->checkAccess('admin')],
    ],
]);
?>

<h1><?php echo $model->title; ?></h1>

<?php
$tags = '';
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->name . ', ';
    $tags = substr($tags, 0, -2);
}
?>

<?php
if (isset($model->audios[0]->location) && isset($model->audios[0]->file) && isset($model->audios[0]->format)) {
    Html::submitButton('Speel audio bestand af', ['class' => 'btn btn-primary']);
    echo Button::widget([
        'label' => 'Speel audio bestand af',
        'options' => ['class' => 'btn btn-primary btn-xs'],
        'url' => 'uploads/audio/' . $model->audios[0]->location . '/' . $model->audios[0]->file . $model->audios[0]->format,
        'htmlOptions' => ['target' => '_blank'],
            ], true);
}else{
    $button = '<span class="null">Niet opgegeven</span>';
}
?>

<?php
echo DetailView::widget([
    'model' => $model,
    'data'=>$model,
    'attributes' => [
        ['label'=>'Uploader', 'value'=>$model->user->username],
        'description:html',
        ['label'=>'Steekwoorden', 'value'=>$tags],
        'year',
        ['name'=>'owner', 'value'=>!empty($model->owner) ? $model->owner : "Niet opgegeven"],
        ['name'=>'created_on', 'value'=>($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")],
        ['name'=>'modified_on', 'value'=>($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")],
        ['label'=>'Bestand', 'value'=>$button, 'type'=>'raw'],
        ['name'=>'published', 'label'=>'Gepubliceerd', 'value'=>$model->published? "Ja":"Nee"]
        ],
]);

//Hier moet een ext. widget voor Ecollection komen
?>
<hr />