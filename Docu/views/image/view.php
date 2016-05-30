<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Image */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Afbeeldingen', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van afbeeldingen', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak afbeeldingen aan', 'url' => ['create'], 'icon' => 'file', 'visible' => Yii::$app->user->getIdentity('user')],   ['label' => 'Bewerk afbeelding', 'url' => ['update','id'=>$model->id], 'pencil' => 'eye-open', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Verwijder afbeelding', 'url' => '#', 'icon' => 'trash', 'linkOptions' => ['submit' => ['delete', 'id' => $model->id]], 'confirm' => 'Weet je zeker dat je deze afbeelding wilt verwijderen?'],
        ['label' => 'Beheer afbeeldingen', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
  ], 
]);
?>

<div class="image-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= Html::img("uploads/afbeeldingen/".$model->images[0]->location.'/'.$model->images[0]->file.$model->images[0]->format); ?>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>
        <?=
    $tags = '';
    foreach ($model->tags as $i => $tag) {
        $tags .= $tag->name . ', ';
        $tags = substr($tags, 0, -2);
    }
    $user = User::findIdentity($model->user_id);
    ?>
    <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'title',
                ['label' => 'Uploader', 'value' => $user->username],
                'description:html',
                ['label' => 'Steekwoorden', 'value' => $tags],
                'year',
                ['label' => 'Eigenaar', 'value' => !empty($model->owner) ? $model->owner : "Niet opgegeven"],
                ['label' => 'Aanmaakdatum', 'value' => ($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")],
                ['label' => 'Laatste wijzigingsdatum', 'value' => ($model->modified_on !== "0000-00-00 00:00:00" ? $model->modified_on : "Niet beschikbaar")],
                ['label' => 'Gepubliceerd', 'label' => 'Gepubliceerd', 'value' => $model->published ? "Ja" : "Nee"],
              ],
        ]);
        ?>
</div>
