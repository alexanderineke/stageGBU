<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Document */

$this->title = 'Bewerk';
$this->params['breadcrumbs'][] = ['label' => 'Documenten', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van documenten', 'url' => ['index'], 'icon' => 'list', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak document aan', 'url' => ['create'], 'icon' => 'file', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('user')],
        ['label' => 'Bewerk document', 'url' => ['update', 'id' => $model->id], 'icon' => 'pencil', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Verwijder document', 'url' => '#', 'icon' => 'trash', 'linkOptions' => ['submit' => ['delete', 'id' => $model->id], 'confirm' => 'Weet je zeker dat je deze document wilt verwijderen?'], 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('admin')],
        ['label' => 'Beheer document', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>

<h1><?= $model->title; ?></h1>

<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_b.jpg', $model->title); ?>
<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_c.jpg', $model->title); ?>
<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_z.jpg', $model->title); ?>
<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'.jpg', $model->title); ?>
<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_n.jpg', $model->title); ?>
<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_m.jpg', $model->title); ?>
<?= ''//Html::img('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_t.jpg', $model->title); ?>

<?php
$tags = '';
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->name . ', ';
    $tags = substr($tags, 0, -2);
}

$user = User::findIdentity($model->id);
?>

<?php
/*
  if (isset($model->documents[0]->location) && isset($model->documents[0]->file) && isset($model->documents[0]->format)) {
  $button = Html::a('Geef document weer', ['uploads/documenten/' . $model->documents[0]->location . '/' . $model->documents[0]->file . $model->documents[0]->format], ['class' => 'btn btn-primary btn-xs', 'target' => '_blank']);
  } else {
  $button = '<span class="null">Niet opgegeven</span>';
  }
 * 
 */
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
        ['label' => 'Laatste wijzigingsdatum', 'value' => ($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")],
        //  ['label' => 'Bestand', 'value' => $button, 'format' => 'raw'],
        ['label' => 'Gepubliceerd', 'label' => 'Gepubliceerd', 'value' => $model->published ? "Ja" : "Nee"]
    ],
]);

//Hier moet een ext. widget voor Ecollection komen
?>

<hr />