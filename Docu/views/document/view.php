<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
use app\models\User;
use yii\helpers\Url;
use yii\bootstrap\Button;
use app\components\ECollection;

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
        //['label' => 'Verwijder document', 'url' => '#', 'icon' => 'trash', 'linkOptions' => ['submit' => ['delete', 'id' => $model->id], 'confirm' => 'Weet je zeker dat je deze document wilt verwijderen?'], 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('admin')],
        ['label' => 'Beheer document', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => !Yii::$app->user->isGuest], //'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>

<h1><?= $model->title; ?></h1>

<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '_b.jpg'); ?>
<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '_c.jpg'); ?>
<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '_z.jpg'); ?>
<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '.jpg'); ?>
<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '_n.jpg'); ?>
<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '_m.jpg'); ?>
<?= Html::img("uploads/documenten/" . $model->documents[0]->location . '/' . $model->documents[0]->file . '_t.jpg'); ?>

<?php
$tags = '';
foreach ($model->tags as $i => $tag)
    $tags .= $tag->name . ', ';
$tags = substr($tags, 0, -2);


$user = User::findIdentity($model->id);
?>

<?php
if (isset($model->documents[0]->location) && isset($model->documents[0]->file) && isset($model->documents[0]->format)) {
    $button = Html::a('Geef document weer', Url::to('@web/uploads/documenten/') . $model->documents[0]->location . '/' . $model->documents[0]->file . $model->documents[0]->format, ['class' => 'btn btn-primary btn-xs', 'target' => '_blank']);
} else {
    $button = '<span class="null">Niet opgegeven</span>';
}
?>

<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        ['label' => 'Titel', 'value' => $model->title],
        ['label' => 'Uploader', 'value' => !empty($user->username) ? $user->username : "Niet opgegeven"],
        'description:html',
        ['label' => 'Steekwoorden', 'value' => $tags],
        ['label' => 'Jaar', 'value' => !empty($model->year) ? $model->year : "Niet opgegeven"],
        ['label' => 'Eigenaar', 'value' => !empty($model->owner) ? $model->owner : "Niet opgegeven"],
        ['label' => 'Aanmaakdatum', 'value' => ($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")],
        ['label' => 'Laatste wijzigingsdatum', 'value' => ($model->modified_on !== "0000-00-00 00:00:00" ? $model->modified_on : "Niet beschikbaar")],
        ['label' => 'Bestand', 'value' => $button, 'format' => 'raw'],
        ['label' => 'Gepubliceerd', 'label' => 'Gepubliceerd', 'value' => $model->published ? "Ja" : "Nee"]
    ],
]);

echo ECollection::widget([
        'file_id' => $model->id,
        'file_type' => 'document',
    ]);
?>

<hr />