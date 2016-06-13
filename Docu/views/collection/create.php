<?php

use yii\helpers\Html;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $model app\models\Collection */

$this->title = 'Maak een collectie';
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
   	['label'=>'Acties','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Lijst van collecties','url'=>['index'],'icon'=>'list','visible'=>Yii::$app->user->getIdentity('moderator')],
	
]]);
?>
<div class="collection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
