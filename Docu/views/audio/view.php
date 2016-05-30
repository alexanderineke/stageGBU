<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
use app\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Audio */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Audiobestanden', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Lijst van audio bestanden', 'url' => ['index'], 'icon' => 'list', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Maak audio bestanden aan', 'url' => ['create'], 'icon' => 'file', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Bewerk audio bestand', 'url' => ['update', 'id' => $model->id], 'icon' => 'pencil', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Verwijder audio bestand', 'url' => '#', 'icon' => 'trash', 'itemOptions' => ['submit' => ['delete', 'id' => $model->id]], 'confirm' => 'Weet je zeker dat je dit audio bestand wilt verwijderen?', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Beheer audio bestand', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => !Yii::$app->user->isGuest],
    ],
]);

$tags = '';
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->name . ', ';
    $tags = substr($tags, 0, -2);
}

$user = User::findIdentity($model->user_id);
?>

<h1><?php echo $model->title; ?></h1>

<?php
if (isset($model->audios->location) && isset($model->audios->file) && isset($model->audios->format)) {
    $button = Html::a('Speel audio bestand af', Url::to('@web/uploads/audio/') . $model->audios->location . '/' . $model->audios->file . $model->audios->format, ['class' => 'btn btn-primary btn-xs']);
} else {
    $button = '<span class="null">Niet opgegeven</span>';
}
?>

<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        ['label' => 'Uploader', 'value' => $user->username],
        'description:html',
        ['label' => 'Steekwoorden', 'value' => $tags],
        'year',
        ['label' => 'Eigenaar', 'value' => !empty($model->owner) ? $model->owner : "Niet opgegeven"],
        ['label' => 'Aanmaakdatum', 'value' => ($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")],
        ['label' => 'Laatste wijzigingsdatum', 'value' => ($model->modified_on !== "0000-00-00 00:00:00" ? $model->modified_on : "Niet beschikbaar")],
        ['label' => 'Bestand', 'value' => $button, 'format' => 'raw'],
        ['label' => 'Gepubliceerd', 'label' => 'Gepubliceerd', 'value' => $model->published ? "Ja" : "Nee"]
    ],
]);

//Hier moet een ext. widget voor Ecollection komen
?>
<hr />